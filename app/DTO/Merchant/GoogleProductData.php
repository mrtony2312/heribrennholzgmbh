<?php

namespace App\DTO\Merchant;

class GoogleProductData
{
    /**
     * @param  list<string>  $additionalImageLinks
     */
    public function __construct(
        public readonly string $id,
        public readonly string $title,
        public readonly string $description,
        public readonly string $link,
        public readonly string $imageLink,
        public readonly array $additionalImageLinks,
        public readonly string $availability,
        public readonly string $condition,
        public readonly string $price,
        public readonly ?string $salePrice,
        public readonly string $brand,
        public readonly ?string $gtin,
        public readonly ?string $mpn,
        public readonly bool $identifierExists,
        public readonly ?string $itemGroupId,
        public readonly string $googleProductCategory,
        public readonly string $productType,
        public readonly ?string $shippingWeight,
        public readonly ?string $unitPricingMeasure = null,
        public readonly ?string $unitPricingBaseMeasure = null,
        public readonly string $contentLanguage,
        public readonly string $targetCountry,
        public readonly float $priceAmount,
        public readonly string $currency,
        public readonly bool $inStock,
    ) {}

    public function schemaAvailability(): string
    {
        return $this->inStock
            ? 'https://schema.org/InStock'
            : 'https://schema.org/OutOfStock';
    }

    public function schemaGtinKey(): ?string
    {
        if ($this->gtin === null) {
            return null;
        }

        $len = strlen($this->gtin);

        return match ($len) {
            8 => 'gtin8',
            12 => 'gtin12',
            13 => 'gtin13',
            14 => 'gtin14',
            default => 'gtin',
        };
    }

    /**
     * @return array<string, mixed>
     */
    public function toJsonLd(): array
    {
        $offer = [
            '@type' => 'Offer',
            'url' => $this->link,
            'price' => number_format($this->priceAmount, 2, '.', ''),
            'priceCurrency' => $this->currency,
            'availability' => $this->schemaAvailability(),
            'itemCondition' => 'https://schema.org/NewCondition',
            'seller' => [
                '@type' => 'Organization',
                'name' => (string) config('merchant.nap.legal_name', config('feed.company.legal_name', config('app.name'))),
            ],
            'hasMerchantReturnPolicy' => $this->returnPolicy(),
            'shippingDetails' => $this->shippingDetails(),
        ];

        $product = [
            '@context' => 'https://schema.org',
            '@type' => 'Product',
            'name' => $this->title,
            'image' => array_values(array_filter([$this->imageLink, ...$this->additionalImageLinks])),
            'description' => $this->description,
            'sku' => $this->mpn ?: $this->id,
            'brand' => [
                '@type' => 'Brand',
                'name' => $this->brand,
            ],
            'offers' => $offer,
        ];

        $gtinKey = $this->schemaGtinKey();
        if ($gtinKey !== null && $this->gtin !== null) {
            $product[$gtinKey] = $this->gtin;
        }
        if ($this->mpn) {
            $product['mpn'] = $this->mpn;
        }

        return $product;
    }

    /**
     * @return array<string, mixed>
     */
    public function returnPolicy(): array
    {
        $customerPays = (bool) config('merchant.returns.customer_pays_return_shipping', true);
        $returnCost = round((float) config('merchant.returns.return_shipping_cost', 0), 2);
        $days = (int) config('merchant.returns.days', config('feed.return_days', 14));

        if ($customerPays && $returnCost <= 0) {
            $returnCost = 19.90;
        }

        $policy = [
            '@type' => 'MerchantReturnPolicy',
            'applicableCountry' => $this->targetCountry !== '' ? $this->targetCountry : 'CH',
            'returnPolicyCategory' => 'https://schema.org/MerchantReturnFiniteReturnWindow',
            'merchantReturnDays' => $days,
            'returnMethod' => 'https://schema.org/ReturnByMail',
            'returnFees' => $customerPays
                ? 'https://schema.org/ReturnShippingFees'
                : 'https://schema.org/FreeReturn',
        ];

        if ($customerPays) {
            $policy['returnShippingFeesAmount'] = [
                '@type' => 'MonetaryAmount',
                'value' => number_format($returnCost, 2, '.', ''),
                'currency' => $this->currency !== '' ? $this->currency : 'CHF',
            ];
        }

        return $policy;
    }

    /**
     * @return array<string, mixed>
     */
    public function shippingDetails(): array
    {
        $country = (string) config('merchant.shipping.country', config('feed.target_country', 'CH'));

        return [
            '@type' => 'OfferShippingDetails',
            'shippingRate' => [
                '@type' => 'MonetaryAmount',
                'value' => number_format((float) config('merchant.shipping.price', config('feed.shipping_price', 0)), 2, '.', ''),
                'currency' => $this->currency !== '' ? $this->currency : 'CHF',
            ],
            'shippingDestination' => [
                '@type' => 'DefinedRegion',
                'addressCountry' => $country,
            ],
            'deliveryTime' => [
                '@type' => 'ShippingDeliveryTime',
                'handlingTime' => [
                    '@type' => 'QuantitativeValue',
                    'minValue' => (int) config('merchant.shipping.handling_min', config('feed.shipping_handling_time_min', 1)),
                    'maxValue' => (int) config('merchant.shipping.handling_max', config('feed.shipping_handling_time_max', 1)),
                    'unitCode' => 'DAY',
                ],
                'transitTime' => [
                    '@type' => 'QuantitativeValue',
                    'minValue' => (int) config('merchant.shipping.transit_min', config('feed.shipping_transit_time_min', 0)),
                    'maxValue' => (int) config('merchant.shipping.transit_max', config('feed.shipping_transit_time_max', 1)),
                    'unitCode' => 'DAY',
                ],
            ],
        ];
    }
}