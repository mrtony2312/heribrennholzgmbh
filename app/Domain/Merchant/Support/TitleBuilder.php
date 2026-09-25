<?php

namespace App\Domain\Merchant\Support;

use Illuminate\Support\Str;

class TitleBuilder
{
    public static function build(string $name, string $brand): string
    {
        $title = html_entity_decode(strip_tags($name), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $title = preg_replace('/\s+/u', ' ', $title) ?? $title;
        $title = trim($title);

        $title = preg_replace('/\b(SOLDE|PROMO|SALE|AKTION|RABATT)\b/iu', '', $title) ?? $title;
        $title = str_replace(['!!!', '…'], '', $title);
        $title = preg_replace('/[\x{1F300}-\x{1FAFF}]/u', '', $title) ?? $title;
        $title = preg_replace('/\s+/u', ' ', $title) ?? $title;
        $title = trim($title, " \t\n\r\0\x0B-–");

        if ($title !== '' && $title === mb_strtoupper($title, 'UTF-8') && preg_match('/[A-ZÄÖÜ]/u', $title)) {
            $title = Str::title(mb_strtolower($title, 'UTF-8'));
        }

        $brand = trim($brand);
        if ($brand !== '' && $brand !== 'Generic' && ! str_starts_with(mb_strtolower($title), mb_strtolower($brand))) {
            $title = $brand.' '.$title;
        }

        return Str::limit(trim($title), 150, '');
    }
}
