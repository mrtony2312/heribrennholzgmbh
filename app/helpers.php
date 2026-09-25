<?php

if (! function_exists('swiss_money')) {
    /**
     * Format a monetary value using Swiss conventions: apostrophe as the
     * thousands separator, a dot as the decimal separator, prefixed with
     * the currency code, e.g. swiss_money(1561.89) === "CHF 1'561.89".
     */
    function swiss_money(float|int|string|null $value, string $currency = 'CHF'): string
    {
        $formatted = number_format((float) $value, 2, '.', "'");

        return trim($currency . ' ' . $formatted);
    }
}

if (! function_exists('vat_included')) {
    /** In einem Bruttobetrag enthaltener MWST-Anteil (Satz aus config/shop.php). */
    function vat_included(float|int|string|null $gross): float
    {
        $rate = (float) config('shop.vat_rate', 8.1);

        return round((float) $gross * $rate / (100 + $rate), 2);
    }
}

if (! function_exists('payment_methods')) {
    /** @return array<string, string> aktive Zahlungsarten: Schlüssel => Bezeichnung */
    function payment_methods(): array
    {
        return collect(config('shop.payment_methods', []))
            ->filter(fn ($m) => $m['enabled'] ?? false)
            ->map(fn ($m) => $m['label'])
            ->all();
    }
}

if (! function_exists('payment_methods_text')) {
    function payment_methods_text(): string
    {
        return implode(', ', payment_methods());
    }
}
