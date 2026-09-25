<?php

namespace App\Services;

use App\Domain\Merchant\GoogleFeedGenerator;
use Illuminate\Support\Facades\Cache;

class ProductFeed
{
    public function __construct(private readonly GoogleFeedGenerator $generator)
    {
    }

    public function toXml(): string
    {
        $ttl = (int) config('feed.cache_ttl');

        if ($ttl > 0) {
            return Cache::remember('product_feed_google_xml', $ttl, fn () => $this->generator->toXml());
        }

        return $this->generator->toXml();
    }

    public function forget(): void
    {
        Cache::forget('product_feed_google_xml');
    }
}
