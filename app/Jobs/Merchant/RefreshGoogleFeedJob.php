<?php

namespace App\Jobs\Merchant;

use App\Services\ProductFeed;
use App\Services\ProductFeedTsv;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Artisan;

class RefreshGoogleFeedJob implements ShouldQueue
{
    use Queueable;

    public function handle(ProductFeed $feed, ProductFeedTsv $tsv): void
    {
        $feed->forget();
        $tsv->forget();
        Artisan::call('merchant:google-feed');
    }
}
