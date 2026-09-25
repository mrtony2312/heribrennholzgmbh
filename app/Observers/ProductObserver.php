<?php

namespace App\Observers;

use App\Models\Product;
use Illuminate\Support\Facades\Cache;

class ProductObserver
{
    public function saved(Product $product): void
    {
        $this->invalidate();
    }

    public function deleted(Product $product): void
    {
        $this->invalidate();
    }

    private function invalidate(): void
    {
        Cache::forget('product_feed_google_xml');
        Cache::forget('product_feed_google_tsv');
    }
}
