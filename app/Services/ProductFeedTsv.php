<?php

namespace App\Services;

use App\Domain\Merchant\GoogleProductMapper;
use App\Domain\Merchant\Support\PriceFormatter;
use App\Models\Product;
use Illuminate\Support\Facades\Cache;

class ProductFeedTsv
{
    /**
     * Tab-delimited Google Merchant feed headers (CH / de / CHF).
     * Only recognized product-data-spec attribute names.
     *
     * @see https://support.google.com/merchants/answer/7052112
     */
    private const HEADERS = [
        'id',
        'title',
        'description',
        'link',
        'image_link',
        'additional_image_link',
        'availability',
        'condition',
        'price',
        'sale_price',
        'brand',
        'gtin',
        'mpn',
        'identifier_exists',
        'item_group_id',
        'google_product_category',
        'product_type',
        'shipping_weight',
        'unit_pricing_measure',
        'unit_pricing_base_measure',
        'min_handling_time',
        'max_handling_time',
        'shipping(country:service:price)',
    ];

    public function __construct(private readonly GoogleProductMapper $mapper)
    {
    }

    public function toTsv(): string
    {
        $ttl = (int) config('feed.cache_ttl');
        $cached = $ttl > 0 ? Cache::get('product_feed_google_tsv') : null;
        if (is_string($cached) && substr_count($cached, "\n") > 1) {
            return $cached;
        }

        $body = $this->build();
        if (substr_count($body, "\n") <= 1) {
            throw new \RuntimeException('Generated Merchant TSV has 0 products.');
        }

        if ($ttl > 0) {
            Cache::put('product_feed_google_tsv', $body, $ttl);
        }

        return $body;
    }

    public function forget(): void
    {
        Cache::forget('product_feed_google_tsv');
    }

    private function build(): string
    {
        $lines = [implode("\t", self::HEADERS)];
        $service = trim((string) config('feed.shipping_service', 'Standardversand'));
        $handlingMin = (int) config('feed.shipping_handling_time_min', 1);
        $handlingMax = (int) config('feed.shipping_handling_time_max', 1);

        Product::with(['images', 'categories'])
            ->orderBy('id')
            ->chunk(200, function ($products) use (&$lines, $service, $handlingMin, $handlingMax) {
                foreach ($products as $product) {
                    $dto = $this->mapper->map($product);
                    if (! $dto) {
                        continue;
                    }

                    $shipPrice = PriceFormatter::format((float) config('feed.shipping_price', 0), $dto->currency);
                    $shipping = sprintf('CH:%s:%s', $service !== '' ? $service : 'Standardversand', $shipPrice);

                    $fields = [
                        $dto->id,
                        $dto->title,
                        $dto->description,
                        $dto->link,
                        $dto->imageLink,
                        implode(',', $dto->additionalImageLinks),
                        $dto->availability,
                        $dto->condition,
                        $dto->price,
                        $dto->salePrice ?? '',
                        $dto->brand,
                        $dto->gtin ?? '',
                        $dto->mpn ?? '',
                        $dto->identifierExists ? 'yes' : 'no',
                        $dto->itemGroupId ?? '',
                        $dto->googleProductCategory,
                        $dto->productType,
                        $dto->shippingWeight ?? '',
                        $dto->unitPricingMeasure ?? '',
                        $dto->unitPricingBaseMeasure ?? '',
                        (string) $handlingMin,
                        (string) $handlingMax,
                        $shipping,
                    ];
                    $lines[] = implode("\t", array_map([$this, 'escape'], $fields));
                }
            });

        return implode("\n", $lines)."\n";
    }

    private function escape(string $value): string
    {
        return str_replace(["\t", "\n", "\r"], ' ', $value);
    }
}
