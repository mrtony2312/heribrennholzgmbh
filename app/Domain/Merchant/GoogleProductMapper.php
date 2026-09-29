<?php

namespace App\Domain\Merchant;

use App\Domain\Merchant\Support\Availability;
use App\Domain\Merchant\Support\DescriptionSanitizer;
use App\Domain\Merchant\Support\Gtin;
use App\Domain\Merchant\Support\PriceFormatter;
use App\Domain\Merchant\Support\TitleBuilder;
use App\Domain\Merchant\Support\UnitPricingResolver;
use App\DTO\Merchant\GoogleProductData;
use App\Models\Product;

class GoogleProductMapper
{
    public function map(Product $product): ?GoogleProductData
    {
        $product->loadMissing(['images', 'categories']);

        if (! $this->isEligible($product)) {
            return null;
        }

        $primary = $product->primary_image;
        $images = $product->images;
        $currency = 'CHF';
        $price = (float) $product->price;
        $regular = (float) ($product->regular_price ?: $product->price);

        // sale_price only when reference / strike-through prices are verified (misrepresentation risk).
        $referenceVerified = (bool) config('merchant.reference_prices_verified', false);
        $onSale = $referenceVerified && (bool) $product->on_sale && $regular > $price;

        $brand = $this->resolveBrand($product);
        $gtin = Gtin::normalize($product->gtin);
        $mpn = trim((string) ($product->sku ?? ''));
        if ($mpn === '') {
            $mpn = 'HB-'.(string) $product->id;
        }

        $unit = UnitPricingResolver::resolve($product);
        // Google shipping_weight max = 1000 kg. Keep unit_pricing; omit overweight shipping_weight.
        [$shippingWeight, $shippingLabel] = $this->shippingWeightAndLabel($unit['shipping_weight'], $unit['measure']);

        $additional = $images
            ->reject(fn ($img) => $primary && $img->id === $primary->id)
            ->take(10)
            ->map(fn ($img) => gmc_absolute_url($img->url))
            ->filter()
            ->values()
            ->all();

        return new GoogleProductData(
            id: (string) $product->id,
            title: TitleBuilder::build((string) $product->name, $brand),
            description: DescriptionSanitizer::plain(
                $product->description ?: $product->short_description,
                (string) $product->name
            ),
            link: gmc_absolute_url(route('product.show', $product->slug)),
            imageLink: gmc_absolute_url($primary->url),
            additionalImageLinks: $additional,
            availability: $product->in_stock ? Availability::InStock->value : Availability::OutOfStock->value,
            condition: (string) config('feed.condition', 'new'),
            price: PriceFormatter::format($onSale ? $regular : $price, $currency),
            salePrice: $onSale ? PriceFormatter::format($price, $currency) : null,
            brand: $brand,
            gtin: $gtin,
            mpn: $mpn,
            identifierExists: true,
            itemGroupId: $product->item_group_id ? (string) $product->item_group_id : null,
            googleProductCategory: $this->googleCategory($product),
            productType: $product->categories->pluck('name')->filter()->implode(' > '),
            shippingWeight: $shippingWeight,
            unitPricingMeasure: $unit['measure'],
            unitPricingBaseMeasure: $unit['base'],
            shippingLabel: $shippingLabel,
            contentLanguage: 'de',
            targetCountry: 'CH',
            priceAmount: $price,
            currency: $currency,
            inStock: (bool) $product->in_stock,
        );
    }

    /**
     * @return array{0: ?string, 1: ?string} [shipping_weight, shipping_label]
     */
    private function shippingWeightAndLabel(?string $shippingWeight, ?string $unitMeasure): array
    {
        $kg = $this->kilogramsFromMeasure($shippingWeight) ?? $this->kilogramsFromMeasure($unitMeasure);

        if ($kg !== null && $kg > 1000) {
            // Over Google's shipping_weight limit — use label instead (oversized / palette freight).
            return [null, 'palette'];
        }

        if ($unitMeasure !== null && str_contains($unitMeasure, 'cbm')) {
            return [$shippingWeight, 'sperrgut'];
        }

        if ($kg !== null && $kg >= 100) {
            return [$shippingWeight, 'palette'];
        }

        return [$shippingWeight, null];
    }

    private function kilogramsFromMeasure(?string $measure): ?float
    {
        if ($measure === null || $measure === '') {
            return null;
        }

        if (! preg_match('/^(\d+(?:\.\d+)?)\s*kg$/iu', trim($measure), $m)) {
            return null;
        }

        return (float) $m[1];
    }

    public function isEligible(Product $product): bool
    {
        $product->loadMissing('images');

        if ((float) $product->price <= 0) {
            return false;
        }

        if (! $product->primary_image) {
            return false;
        }

        $excluded = array_map('mb_strtolower', (array) config('feed.excluded_brands', []));
        if ($excluded !== [] && in_array(mb_strtolower($this->resolveBrand($product)), $excluded, true)) {
            return false;
        }

        $type = strtolower((string) $product->type);

        if (in_array($type, ['digital', 'service', 'voucher', 'gift_card'], true)) {
            return false;
        }

        return true;
    }

    private function resolveBrand(Product $product): string
    {
        $known = (array) config('feed.known_brands', []);
        $name = (string) $product->name;

        foreach ($known as $brand) {
            if (stripos($name, (string) $brand) !== false) {
                return (string) $brand;
            }
        }

        $fallback = trim((string) config('feed.brand', config('merchant.default_brand', 'Heri Brennholz')));

        return $fallback !== '' ? $fallback : 'Heri Brennholz';
    }

    private function googleCategory(Product $product): string
    {
        $map = (array) config('feed.google_product_category_map', []);
        $slug = $product->categories->first()?->slug;

        if ($slug !== null && isset($map[$slug])) {
            return (string) $map[$slug];
        }

        return (string) config('feed.google_product_category', '6229');
    }
}
