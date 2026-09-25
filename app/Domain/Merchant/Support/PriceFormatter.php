<?php

namespace App\Domain\Merchant\Support;

class PriceFormatter
{
    public static function format(float $amount, string $currency): string
    {
        return number_format($amount, 2, '.', '').' '.$currency;
    }

    public static function amount(float $amount): string
    {
        return number_format($amount, 2, '.', '');
    }
}
