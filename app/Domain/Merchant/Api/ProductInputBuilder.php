<?php

namespace App\Domain\Merchant\Api;

use App\Domain\Merchant\Support\Availability;
use App\DTO\Merchant\GoogleProductData;

/**
 * Maps feed DTO → Merchant API ProductInput JSON body (CH market).
 *
 * @see https://developers.google.com/merchant/api/reference/rest/products_v1/ProductAttributes
 */
class ProductInputBuilder
{
    /**
     * @return array{offerId: string, contentLanguage: string, feedLabel: string, productAttributes: array<string, mixed>}
     */
    public function build(GoogleProductData $item): array
    {
        $attributes = [
            'title' => $item->title,
            'description' => $item->description,
            'link' => $item->link,
            'imageLink' => $item->imageLink,
            'availability' => $this->availability($item->availability),
            'condition' => strtoupper($item->condition),
            'price' => $this->price($item->price),
        ];

        if ($item->additionalImageLinks !== []) {
            $attributes['additionalImageLinks'] = array_values($item->additionalImageLinks);
        }

        if ($item->salePrice) {
            $attributes['salePrice'] = $this->price($item->salePrice);
        }

        if ($item->brand !== '') {
            $attributes['brand'] = $item->brand;
        }

        if ($item->gtin) {
            $attributes['gtins'] = [$item->gtin];
        }

        if ($item->mpn) {
            $attributes['mpn'] = $item->mpn;
        }

        if (! $item->identifierExists) {
            $attributes['identifierExists'] = false;
        }

        if ($item->googleProductCategory !== '') {
            $attributes['googleProductCategory'] = $item->googleProductCategory;
        }

        if ($item->productType !== '') {
            $attributes['productTypes'] = [$item->productType];
        }

        $shippingWeight = $this->parseMeasure($item->shippingWeight);
        if ($shippingWeight) {
            $attributes['shippingWeight'] = $shippingWeight;
        }

        $currency = $item->currency !== ''
            ? $item->currency
            : (string) config('merchant.currency', 'CHF');

        $attributes['shipping'] = [[
            'country' => (string) config('merchant.shipping.country', 'CH'),
            'service' => (string) config('merchant.shipping.service', config('feed.shipping_service')),
            'price' => $this->money((float) config('merchant.shipping.price', 0), $currency),
            'minHandlingTime' => (string) (int) config('merchant.shipping.handling_min', 1),
            'maxHandlingTime' => (string) (int) config('merchant.shipping.handling_max', 1),
            'minTransitTime' => (string) (int) config('merchant.shipping.transit_min', 0),
            'maxTransitTime' => (string) (int) config('merchant.shipping.transit_max', 1),
        ]];

        return [
            'offerId' => $item->id,
            'contentLanguage' => (string) config('merchant.content_language', 'de'),
            'feedLabel' => (string) config('merchant.feed_label', 'CH'),
            'productAttributes' => $attributes,
        ];
    }

    private function availability(string $availability): string
    {
        $normalized = Availability::tryFrom($availability)?->value ?? $availability;

        return match ($normalized) {
            Availability::InStock->value, 'in_stock' => 'IN_STOCK',
            Availability::OutOfStock->value, 'out_of_stock' => 'OUT_OF_STOCK',
            Availability::Preorder->value, 'preorder' => 'PREORDER',
            Availability::Backorder->value, 'backorder' => 'BACKORDER',
            default => 'IN_STOCK',
        };
    }

    /**
     * @return array{amountMicros: string, currencyCode: string}
     */
    private function price(string $googlePrice): array
    {
        // "29.99 CHF"
        if (! preg_match('/^(\d+(?:\.\d{1,2})?)\s+([A-Z]{3})$/', trim($googlePrice), $m)) {
            throw new \InvalidArgumentException('Invalid Google price format: '.$googlePrice);
        }

        return $this->money((float) $m[1], $m[2]);
    }

    /**
     * @return array{amountMicros: string, currencyCode: string}
     */
    private function money(float $amount, string $currency): array
    {
        return [
            'amountMicros' => (string) (int) round($amount * 1_000_000),
            'currencyCode' => $currency,
        ];
    }

    /**
     * Feed string "990 kg" → API {value, unit}.
     *
     * @return array{value: float, unit: string}|null
     */
    private function parseMeasure(?string $measure): ?array
    {
        if ($measure === null || $measure === '') {
            return null;
        }

        if (! preg_match('/^(\d+(?:\.\d+)?)\s*([a-z]+)$/i', trim($measure), $m)) {
            return null;
        }

        return [
            'value' => (float) $m[1],
            'unit' => strtolower($m[2]),
        ];
    }
}
