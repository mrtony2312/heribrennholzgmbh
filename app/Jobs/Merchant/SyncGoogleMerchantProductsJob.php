<?php

namespace App\Jobs\Merchant;

use App\Domain\Merchant\Api\MerchantProductSync;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class SyncGoogleMerchantProductsJob implements ShouldQueue
{
    use Queueable;

    public function handle(MerchantProductSync $sync): void
    {
        if (! config('merchant.api.enabled')) {
            return;
        }

        $result = $sync->sync(writeFeed: true);

        Log::info('Merchant API sync finished (CH)', [
            'ok' => $result['ok'],
            'skipped' => $result['skipped'],
            'failed' => $result['failed'],
        ]);
    }
}
