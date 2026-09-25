<?php

namespace App\Console\Commands\Merchant;

use App\Domain\Merchant\Api\MerchantApiClient;
use App\Domain\Merchant\Api\MerchantProductSync;
use Illuminate\Console\Command;

class SyncGoogleMerchantApiCommand extends Command
{
    protected $signature = 'merchant:sync-api
        {--dry-run : Build payloads but do not call Google}
        {--no-feed : Do not rewrite public/feeds/google-shopping.xml}';

    protected $description = 'Push the CH catalog to Google Merchant Center via Merchant API.';

    public function handle(MerchantApiClient $client, MerchantProductSync $sync): int
    {
        if (! config('merchant.api.enabled') && ! $this->option('dry-run')) {
            $this->error('MERCHANT_API_ENABLED is false. Set it to true in .env after credentials are ready.');

            return self::FAILURE;
        }

        if ((string) config('merchant.target_country') !== 'CH') {
            $this->error('Refusing sync: MERCHANT_TARGET_COUNTRY must be CH.');

            return self::FAILURE;
        }

        $status = $client->status();
        $this->table(
            ['Key', 'Value'],
            [
                ['account_id', $status['account_id'] ?: '(missing)'],
                ['data_source', $status['data_source'] ?: '(missing)'],
                ['auth_mode', $status['auth_mode']],
                ['target_country', $status['target_country']],
                ['feed_label', $status['feed_label']],
                ['currency', $status['currency']],
                ['configured', $status['configured'] ? 'yes' : 'no'],
                ['ready_to_sync', $status['ready_to_sync'] ? 'yes' : 'no'],
            ]
        );

        if ($this->option('dry-run')) {
            $this->info('Dry-run only — no API calls.');

            return self::SUCCESS;
        }

        if (! $client->isConfigured()) {
            $this->error('Merchant API credentials are incomplete. See .env.example (MERCHANT_* / GOOGLE_*).');

            return self::FAILURE;
        }

        if ($status['data_source_id'] === '') {
            $this->error('Set MERCHANT_DATA_SOURCE_ID before syncing.');

            return self::FAILURE;
        }

        $this->info('Syncing products to Merchant Center (CH)…');
        $result = $sync->sync(writeFeed: ! $this->option('no-feed'));

        $this->info(sprintf(
            'Done — ok: %d, skipped: %d, failed: %d',
            $result['ok'],
            $result['skipped'],
            $result['failed']
        ));

        foreach (array_slice($result['errors'], 0, 20) as $error) {
            $this->warn($error);
        }

        if (count($result['errors']) > 20) {
            $this->warn('… and '.(count($result['errors']) - 20).' more (see laravel.log)');
        }

        return $result['failed'] > 0 ? self::FAILURE : self::SUCCESS;
    }
}
