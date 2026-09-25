<?php

namespace App\Domain\Merchant\Api;

use App\Domain\Merchant\GoogleFeedValidator;
use App\Domain\Merchant\GoogleProductMapper;
use App\Models\Product;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Throwable;

class MerchantProductSync
{
    public function __construct(
        private MerchantApiClient $client,
        private ProductInputBuilder $builder,
        private GoogleProductMapper $mapper,
        private GoogleFeedValidator $validator,
    ) {}

    /**
     * Push every eligible catalog product to Merchant API.
     * Optionally refresh the XML feed on disk afterwards.
     *
     * @return array{ok: int, skipped: int, failed: int, errors: list<string>}
     */
    public function sync(bool $writeFeed = true): array
    {
        $ok = 0;
        $skipped = 0;
        $failed = 0;
        $errors = [];

        Product::with(['images', 'categories'])
            ->orderBy('id')
            ->chunkById(100, function ($products) use (&$ok, &$skipped, &$failed, &$errors) {
                foreach ($products as $product) {
                    $dto = $this->mapper->map($product);
                    $label = (string) $product->id;

                    if ($dto === null) {
                        $skipped++;

                        continue;
                    }

                    $issues = $this->validator->validateDto($dto);
                    if ($issues !== []) {
                        $skipped++;
                        $errors[] = $dto->id.': skipped — '.implode('; ', $issues);

                        continue;
                    }

                    try {
                        $payload = $this->builder->build($dto);
                        $this->client->insertProductInput($payload);
                        $ok++;
                    } catch (Throwable $e) {
                        $failed++;
                        $errors[] = $dto->id.': '.$e->getMessage();
                        Log::error('Merchant API sync failed for '.$dto->id, [
                            'exception' => $e->getMessage(),
                        ]);
                    }
                }
            });

        if ($writeFeed) {
            try {
                Artisan::call('merchant:google-feed');
            } catch (Throwable $e) {
                $errors[] = 'XML feed write failed: '.$e->getMessage();
            }
        }

        return compact('ok', 'skipped', 'failed', 'errors');
    }
}
