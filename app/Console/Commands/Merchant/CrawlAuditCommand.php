<?php

namespace App\Console\Commands\Merchant;

use App\Domain\Merchant\GoogleFeedValidator;
use App\Domain\Merchant\GoogleProductMapper;
use App\Models\Product;
use Illuminate\Console\Command;

/**
 * Crawl-style Merchant Center suspension audit for the Swiss catalog.
 */
class CrawlAuditCommand extends Command
{
    protected $signature = 'merchant:crawl-audit {--json : Machine-readable JSON}';

    protected $description = 'Crawl-style audit of suspension risks (feed, brand, CH market alignment).';

    public function handle(GoogleProductMapper $mapper, GoogleFeedValidator $validator): int
    {
        $findings = [
            'critical' => [],
            'high' => [],
            'medium' => [],
            'stats' => [
                'products' => 0,
                'eligible' => 0,
                'no_identifier' => 0,
            ],
        ];

        $appName = (string) config('app.name');
        $legal = (string) config('merchant.nap.legal_name');
        $country = (string) config('merchant.target_country');
        $currency = (string) config('merchant.currency');

        if ($country !== 'CH') {
            $findings['critical'][] = "MERCHANT_TARGET_COUNTRY=\"{$country}\" must be CH for this storefront.";
        }
        if ($currency !== 'CHF') {
            $findings['critical'][] = "MERCHANT_CURRENCY=\"{$currency}\" must be CHF for Switzerland.";
        }
        if (stripos($legal, 'Casacuberta') !== false || stripos($appName, 'Casacuberta') !== false) {
            $findings['critical'][] = 'Spanish Casacuberta branding detected — must use Heri Brennholz GmbH (CH).';
        }
        if (stripos($appName, 'Naturalenha') !== false || stripos($appName, 'Lenha') !== false) {
            $findings['critical'][] = 'Portuguese / legacy brand detected in APP_NAME — must use Heri Brennholz.';
        }
        if ($legal !== '' && stripos($legal, 'Heri Brennholz') === false) {
            $findings['high'][] = "merchant.nap.legal_name=\"{$legal}\" should be Heri Brennholz GmbH.";
        }
        if ((string) config('merchant.nap.country') !== 'CH') {
            $findings['critical'][] = 'merchant.nap.country must be CH.';
        }
        if ((string) config('merchant.shipping.country') !== 'CH') {
            $findings['critical'][] = 'merchant.shipping.country must be CH.';
        }

        $returnCost = (float) config('merchant.returns.return_shipping_cost', 0);
        $customerPays = (bool) config('merchant.returns.customer_pays_return_shipping', true);
        if ($customerPays && $returnCost <= 0) {
            $findings['high'][] = 'Return policy says customer pays shipping but MERCHANT_RETURN_SHIPPING_COST is 0.';
        }

        Product::with(['images', 'categories'])->orderBy('id')->chunkById(100, function ($products) use ($mapper, $validator, &$findings) {
            foreach ($products as $product) {
                $findings['stats']['products']++;
                $dto = $mapper->map($product);

                if ($dto === null) {
                    $findings['high'][] = 'Ineligible for feed: '.$product->id.' '.($product->name ?? '');

                    continue;
                }

                $findings['stats']['eligible']++;

                foreach ($validator->validateDto($dto) as $issue) {
                    $findings['high'][] = "{$dto->id}: {$issue}";
                }

                if ($dto->targetCountry !== 'CH') {
                    $findings['critical'][] = "Product {$dto->id} target_country={$dto->targetCountry} (expected CH).";
                }

                if ($dto->currency !== 'CHF') {
                    $findings['high'][] = "Product {$dto->id} currency={$dto->currency} (expected CHF).";
                }

                if (! $dto->identifierExists) {
                    $findings['stats']['no_identifier']++;
                    $findings['medium'][] = "No GTIN/MPN: {$dto->id} ".($product->name ?? '');
                }
            }
        });

        if ($this->option('json')) {
            $this->line(json_encode($findings, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        } else {
            $this->table(['Metric', 'Value'], collect($findings['stats'])->map(fn ($v, $k) => [$k, $v])->values()->all());
            foreach (['critical', 'high', 'medium'] as $level) {
                if ($findings[$level] === []) {
                    continue;
                }
                $this->newLine();
                $this->warn(strtoupper($level).' ('.count($findings[$level]).')');
                foreach (array_slice($findings[$level], 0, 40) as $line) {
                    $this->line(' - '.$line);
                }
                if (count($findings[$level]) > 40) {
                    $this->line(' - … '.(count($findings[$level]) - 40).' more');
                }
            }
            if ($findings['critical'] === [] && $findings['high'] === []) {
                $this->info('No critical/high suspension blockers in local CH catalog.');
            }
        }

        return $findings['critical'] === [] ? self::SUCCESS : self::FAILURE;
    }
}
