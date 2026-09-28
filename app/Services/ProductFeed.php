<?php

namespace App\Services;

use App\Domain\Merchant\GoogleFeedGenerator;
use App\Domain\Merchant\SafeFeedWriter;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class ProductFeed
{
    public function __construct(
        private readonly GoogleFeedGenerator $generator,
        private readonly SafeFeedWriter $safeWriter,
    ) {
    }

    public function toXml(): string
    {
        $ttl = (int) config('feed.cache_ttl');

        if ($ttl > 0) {
            $cached = Cache::get('product_feed_google_xml');
            if (is_string($cached) && $this->safeWriter->countItems($cached) > 0) {
                return $cached;
            }
        }

        try {
            $xml = $this->generator->toXml();
            $items = $this->safeWriter->countItems($xml);

            if ($items < 1) {
                throw new RuntimeException('Generated Merchant feed has 0 products.');
            }

            if ($ttl > 0) {
                Cache::put('product_feed_google_xml', $xml, $ttl);
            }

            return $xml;
        } catch (RuntimeException $e) {
            Log::error('Merchant feed generation failed — serving last-good if available', [
                'message' => $e->getMessage(),
            ]);

            $fallback = $this->safeWriter->loadLastGoodXml();
            if ($fallback !== null) {
                return $fallback;
            }

            throw $e;
        }
    }

    public function forget(): void
    {
        Cache::forget('product_feed_google_xml');
    }
}
