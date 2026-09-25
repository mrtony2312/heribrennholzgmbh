<?php

namespace App\Domain\Merchant\Support;

class Gtin
{
    public static function isValid(?string $value): bool
    {
        $digits = preg_replace('/\D+/', '', (string) $value) ?? '';
        $len = strlen($digits);

        if (! in_array($len, [8, 12, 13, 14], true)) {
            return false;
        }

        $check = (int) $digits[$len - 1];
        $sum = 0;
        $body = substr($digits, 0, -1);

        for ($i = 0; $i < strlen($body); $i++) {
            $n = (int) $body[strlen($body) - 1 - $i];
            $sum += ($i % 2 === 0) ? $n * 3 : $n;
        }

        $expected = (10 - ($sum % 10)) % 10;

        return $check === $expected;
    }

    public static function normalize(?string $value): ?string
    {
        $digits = preg_replace('/\D+/', '', (string) $value) ?? '';

        return self::isValid($digits) ? $digits : null;
    }
}
