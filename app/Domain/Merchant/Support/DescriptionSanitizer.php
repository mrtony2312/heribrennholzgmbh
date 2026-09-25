<?php

namespace App\Domain\Merchant\Support;

use Illuminate\Support\Str;

class DescriptionSanitizer
{
    public static function plain(?string $html, string $fallback = ''): string
    {
        $text = html_entity_decode(strip_tags((string) $html), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = preg_replace('/https?:\/\/\S+/iu', '', $text) ?? $text;
        $text = preg_replace('/\s+/u', ' ', $text) ?? $text;
        $text = trim($text);

        $replacements = [
            '/Bearbeitungszeit[^.]{0,80}\./iu' => '',
            '/Lieferung in 2 bis 4 Werktagen[^.]*\./iu' => '',
            '/Kostenlose Lieferung auf Palette in die Schweiz und nach Deutschland\./iu' => '',
            '/in die Schweiz und nach (Deutschland|Liechtenstein)/iu' => 'in die Schweiz',
            '/sowie nach Liechtenstein/iu' => '',
            '/und nach Liechtenstein/iu' => '',
            '/\b(guérit|miracle|perte de poids garantie|replica|1:1|authentic AAA)\b/iu' => '',
        ];

        foreach ($replacements as $pattern => $replacement) {
            $text = preg_replace($pattern, $replacement, $text) ?? $text;
        }

        $suffix = ' Kostenlose Lieferung innerhalb der Schweiz in 1 bis 2 Werktagen.';
        if (! str_contains(mb_strtolower($text), '1 bis 2 werktag')) {
            $text = trim($text).$suffix;
        }

        $text = preg_replace('/\s+/u', ' ', $text) ?? $text;
        $text = trim($text);

        if ($text === '') {
            $text = trim($fallback.$suffix);
        }

        return Str::limit($text, 1500, '');
    }
}
