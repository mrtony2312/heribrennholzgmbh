<?php

namespace App\Console\Commands\Merchant;

use App\Domain\Merchant\Api\MerchantApiClient;
use Illuminate\Console\Command;
use Throwable;

class VerifyMerchantApiCommand extends Command
{
    protected $signature = 'merchant:api-status';

    protected $description = 'Validate Merchant API OAuth / service-account credentials and account access (CH).';

    public function handle(MerchantApiClient $client): int
    {
        $status = $client->status();

        $this->table(
            ['Key', 'Value'],
            [
                ['MERCHANT_API_ENABLED', config('merchant.api.enabled') ? 'true' : 'false'],
                ['auth_mode', $status['auth_mode']],
                ['account_id', $status['account_id'] ?: '(missing)'],
                ['data_source_id', $status['data_source_id'] ?: '(missing)'],
                ['target_country', $status['target_country']],
                ['content_language', $status['content_language']],
                ['feed_label', $status['feed_label']],
                ['currency', $status['currency']],
                ['GOOGLE_CLIENT_ID', $status['oauth_client_id']],
                ['GOOGLE_CLIENT_SECRET', $status['oauth_secret']],
                ['GOOGLE_REFRESH_TOKEN', $status['oauth_refresh']],
                ['service_account', $status['email'] ?: '(none)'],
                ['configured', $status['configured'] ? 'yes' : 'no'],
                ['ready_to_sync', $status['ready_to_sync'] ? 'yes' : 'no'],
            ]
        );

        if ((string) config('merchant.target_country') !== 'CH') {
            $this->error('MERCHANT_TARGET_COUNTRY must be CH for this storefront (misrepresentation risk).');

            return self::FAILURE;
        }

        if (! $client->isConfigured()) {
            $this->error('Not configured. Same path as Naturalenha / Casacuberta:');
            $this->line('1. GOOGLE_CLIENT_ID + GOOGLE_CLIENT_SECRET + GOOGLE_REFRESH_TOKEN (OAuth Playground)');
            $this->line('   OR service-account JSON at storage/app/google/merchant-sa.json');
            $this->line('2. MERCHANT_ACCOUNT_ID = Merchant Center account id (Swiss MC)');
            $this->line('3. MERCHANT_DATA_SOURCE_ID = API primary data source id');
            $this->line('4. MERCHANT_API_ENABLED=true');

            return self::FAILURE;
        }

        try {
            $token = $client->accessToken();
            $this->info('Access token OK ('.$status['auth_mode'].', '.strlen($token).' chars).');
        } catch (Throwable $e) {
            $this->error('Token fetch failed: '.$e->getMessage());

            return self::FAILURE;
        }

        try {
            $account = $client->getAccount();
            $name = $account['accountName'] ?? $account['name'] ?? json_encode($account);
            $this->info('Account reachable: '.$name);
        } catch (Throwable $e) {
            $this->warn('accounts.get failed: '.$e->getMessage());
            $this->line('If GCP_NOT_REGISTERED: register the GCP project as developer on THIS Merchant Center.');
            $this->line('https://developers.google.com/merchant/api/guides/quickstart/direct-api-calls#step_1_register_as_a_developer');

            return self::FAILURE;
        }

        if ($status['data_source_id'] === '') {
            $this->warn('Missing MERCHANT_DATA_SOURCE_ID — create an API primary data source, then sync.');
        }

        return self::SUCCESS;
    }
}
