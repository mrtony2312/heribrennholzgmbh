<?php

namespace App\Domain\Merchant\Support;

use App\Models\Product;

/**
 * Resolves unit_pricing_measure / unit_pricing_base_measure for CH (EFTA).
 *
 * @see https://support.google.com/merchants/answer/6324455
 * @see https://support.google.com/merchants/answer/6324490
 */
class UnitPricingResolver
{
    /**
     * @return array{measure: ?string, base: ?string, shipping_weight: ?string}
     */
    public static function resolve(Product $product): array
    {
        // Appliances / stoves are sold as pieces — unit pricing by weight/volume does not apply.
        if (self::isPieceGoods($product)) {
            return ['measure' => null, 'base' => null, 'shipping_weight' => self::shippingWeightOnly($product)];
        }

        $text = trim(implode(' ', array_filter([
            (string) $product->name,
            (string) ($product->formatted_weight ?? ''),
            strip_tags((string) ($product->short_description ?? '')),
        ])));

        $kg = self::parseTotalKilograms($text, $product);
        if ($kg !== null && $kg > 0) {
            $measure = self::formatMeasure($kg, 'kg');

            return [
                'measure' => $measure,
                'base' => '1 kg',
                'shipping_weight' => $measure,
            ];
        }

        $cbm = self::parseCubicMeters($text, $product);
        if ($cbm !== null && $cbm > 0) {
            $measure = self::formatMeasure($cbm, 'cbm');

            return [
                'measure' => $measure,
                'base' => '1 cbm',
                'shipping_weight' => null,
            ];
        }

        return [
            'measure' => null,
            'base' => null,
            'shipping_weight' => self::shippingWeightOnly($product),
        ];
    }

    private static function isPieceGoods(Product $product): bool
    {
        $slug = strtolower((string) $product->categories->first()?->slug);
        if (in_array($slug, ['pelletoefen', 'kaminoefen', 'kamineinsaetze', 'oefen', 'zubehoer'], true)) {
            return true;
        }

        $type = strtolower((string) ($product->type ?? ''));

        return in_array($type, ['stove', 'appliance', 'accessory'], true);
    }

    private static function parseTotalKilograms(string $text, Product $product): ?float
    {
        // 70 Säcke à 15 kg / 66 sacs de 15kg / 70x15kg
        if (preg_match(
            '/(\d+)\s*(?:Säcke|Saecke|Sacks?|Bags?|sacs?)\s*(?:à|a|de|x|×|\*)\s*(\d+(?:[.,]\d+)?)\s*kg/iu',
            $text,
            $m
        )) {
            return (float) str_replace(',', '.', $m[1]) * (float) str_replace(',', '.', $m[2]);
        }

        // Explicit total tonnage
        if (preg_match('/(\d+(?:[.,]\d+)?)\s*(?:t|tonnen|tonnes)\b/iu', $text, $m)) {
            return (float) str_replace(',', '.', $m[1]) * 1000;
        }

        // "1050 kg" / "975kg" as a standalone total (avoid matching only the bag size when bags count exists without kg)
        if (! preg_match('/\b(?:Säcke|Saecke|Sacks?|Bags?|sacs?)\b/iu', $text)
            && preg_match('/(\d+(?:[.,]\d+)?)\s*kg\b/iu', $text, $m)
        ) {
            $value = (float) str_replace(',', '.', $m[1]);
            if ($value >= 1) {
                return $value;
            }
        }

        // Bags count without per-bag kg → derive from Grundpreis CHF/kg
        if (preg_match('/(\d+)\s*(?:Säcke|Saecke|Sacks?|Bags?|sacs?)\b/iu', $text)
            && self::labelIsPerKg($product)
            && ($derived = self::deriveFromUnitPrice($product)) !== null
        ) {
            return $derived;
        }

        // Derive from price ÷ price_per_unit when label is CHF/kg
        if (self::labelIsPerKg($product) && ($derived = self::deriveFromUnitPrice($product)) !== null) {
            return $derived;
        }

        // DB weight only when it looks like a total (not a single 15 kg sack on a palette)
        if (is_numeric($product->weight)) {
            $w = (float) $product->weight;
            if ($w >= 50 || ! preg_match('/\b(?:Säcke|Saecke|Palette|sacs?)\b/iu', $text)) {
                return $w > 0 ? $w : null;
            }
        }

        return null;
    }

    private static function parseCubicMeters(string $text, Product $product): ?float
    {
        if (preg_match('/(\d+(?:[.,]\d+)?)\s*m\s*[³3]\b/iu', $text, $m)) {
            return (float) str_replace(',', '.', $m[1]);
        }

        // Swiss Ster / Raummeter ≈ 1 m³ for retail unit-price display (Google unit: cbm)
        if (preg_match('/(\d+(?:[.,]\d+)?)\s*(?:Ster|Rm|Raummeter)\b/iu', $text, $m)) {
            return (float) str_replace(',', '.', $m[1]);
        }

        if (self::labelIsPerSter($product) && ($derived = self::deriveFromUnitPrice($product)) !== null) {
            return $derived;
        }

        return null;
    }

    private static function labelIsPerKg(Product $product): bool
    {
        $label = mb_strtolower((string) ($product->price_per_unit_label ?? ''));

        return $label !== '' && str_contains($label, '/kg');
    }

    private static function labelIsPerSter(Product $product): bool
    {
        $label = mb_strtolower((string) ($product->price_per_unit_label ?? ''));

        return $label !== '' && (str_contains($label, '/ster') || str_contains($label, '/rm') || str_contains($label, '/m³') || str_contains($label, '/m3'));
    }

    private static function deriveFromUnitPrice(Product $product): ?float
    {
        $ppu = (float) ($product->price_per_unit ?? 0);
        $price = (float) $product->price;

        if ($ppu <= 0 || $price <= 0) {
            return null;
        }

        $qty = $price / $ppu;
        if ($qty < 0.01 || $qty > 100000) {
            return null;
        }

        return round($qty, 3);
    }

    private static function shippingWeightOnly(Product $product): ?string
    {
        if (! is_numeric($product->weight) || (float) $product->weight <= 0) {
            return null;
        }

        return self::formatMeasure((float) $product->weight, 'kg');
    }

    private static function formatMeasure(float $value, string $unit): string
    {
        $formatted = rtrim(rtrim(number_format($value, 3, '.', ''), '0'), '.');

        // Google examples use a space before the unit (e.g. "3 kg", "1.5 kg").
        return $formatted.' '.$unit;
    }
}
