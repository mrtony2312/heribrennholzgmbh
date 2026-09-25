<?php

namespace App\Services;

use App\Domain\Merchant\GoogleProductMapper;
use App\Models\Product;
use Illuminate\Support\Facades\Cache;

class ProductFeedTsv
{
    private const HEADERS = [
        'id', 'title', 'description', 'link', 'image_link', 'availability',
        'price', 'sale_price', 'brand', 'gtin', 'mpn', 'condition',
        'google_product_category', 'product_type', 'item_group_id',
        'shipping(country:service:price)', 'identifier_exists',
    ];

    public function __construct(private readonly GoogleProductMapper $mapper)
    {
    }

    public function toTsv(): string
    {
        $ttl = (int) config('feed.cache_ttl');

        if ($ttl > 0) {
            return Cache::remember('product_feed_google_tsv', $ttl, fn () => $this->build());
        }

        return $this->build();
    }

    public function forget(): void
    {
        Cache::forget('product_feed_google_tsv');
    }

    private function build(): string
    {
        $lines = [implode("\t", self::HEADERS)];
        $service = (string) config('feed.shipping_service');
        $countries = (array) config('feed.shipping_countries', ['CH']);

        Product::with(['images', 'categories'])
            ->orderBy('id')
            ->chunk(200, function ($products) use (&$lines, $service, $countries) {
                foreach ($products as $product) {
                    $dto = $this->mapper->map($product);
                    if (! $dto) {
                        continue;
                    }
                    $shipping = collect($countries)
                        ->map(fn ($country) => sprintf('%s:%s:%s', $country, $service, number_format((float) config('feed.shipping_price', 0), 2, '.', '').' '.$dto->currency))
                        ->implode(';');

                    $fields = [
                        $dto->id,
                        $dto->title,
                        $dto->description,
                        $dto->link,
                        $dto->imageLink,
                        $dto->availability,
                        $dto->price,
                        $dto->salePrice ?? '',
                        $dto->brand,
                        $dto->gtin ?? '',
                        $dto->mpn ?? '',
                        $dto->condition,
                        $dto->googleProductCategory,
                        $dto->productType,
                        $dto->itemGroupId ?? '',
                        $shipping,
                        $dto->identifierExists ? 'yes' : 'no',
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
