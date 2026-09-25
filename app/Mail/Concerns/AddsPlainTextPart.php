<?php

namespace App\Mail\Concerns;

use Closure;
use Symfony\Component\Mime\Email;

trait AddsPlainTextPart
{
    // HTML-only mails are a spam signal; derive a text/plain alternative from the rendered HTML.
    protected function plainTextPart(): Closure
    {
        return function (Email $message): void {
            $html = $message->getHtmlBody();
            if (! is_string($html) || $message->getTextBody()) {
                return;
            }

            $html = preg_replace('#<head\b.*?</head>|<span[^>]*display:none.*?</span>#is', '', $html);
            $html = preg_replace('#<a\b[^>]*href="(?:mailto:|tel:)?([^"]+)"[^>]*>(.*?)</a>#is', '$2 ($1)', $html);
            $html = preg_replace('#<br\s*/?>|</(?:tr|p|div|h[1-6]|li)>#i', "\n", $html);
            $text = html_entity_decode(strip_tags($html), ENT_QUOTES | ENT_HTML5, 'UTF-8');
            $text = preg_replace("/[ \t]+/", ' ', $text);
            $text = preg_replace("/ *\n[ \n]*/", "\n", $text);

            $message->text(trim($text));
        };
    }
}
