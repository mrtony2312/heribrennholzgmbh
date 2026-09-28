<?php

namespace App\Console\Commands\Merchant;

use App\Domain\Merchant\GoogleProductMapper;
use App\Domain\Merchant\SafeFeedWriter;
use App\Models\Product;
use App\Services\ProductFeed;
use App\Services\ProductFeedTsv;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Throwable;

class GenerateGoogleFeedCommand extends Command
{
    protected $signature = 'merchant:google-feed';

    protected $description = 'Generate the Google Merchant Center XML/TSV feeds (CH / de / CHF) with empty-feed protection';

    public function handle(
        ProductFeed $feed,
        ProductFeedTsv $tsv,
        GoogleProductMapper $mapper,
        SafeFeedWriter $writer,
    ): int {
        $feed->forget();
        $tsv->forget();

        $eligible = 0;
        $withUnit = 0;
        Product::with(['images', 'categories'])->orderBy('id')->chunk(200, function ($products) use ($mapper, &$eligible, &$withUnit) {
            foreach ($products as $product) {
                $dto = $mapper->map($product);
                if (! $dto) {
                    continue;
                }
                $eligible++;
                if ($dto->unitPricingMeasure) {
                    $withUnit++;
                }
            }
        });

        if ($eligible < 1) {
            $this->error('Abort: 0 eligible products — existing feed files were NOT overwritten.');

            return self::FAILURE;
        }

        try {
            $built = $writer->buildXml();
            $xml = $built['xml'];
            $items = $built['items'];

            $tsvBody = $tsv->toTsv();

            $storageDir = storage_path('app/feeds');
            $publicDir = public_path('feeds');
            File::ensureDirectoryExists($storageDir);
            File::ensureDirectoryExists($publicDir);

            $writer->writeXmlAtomically($xml, [
                $storageDir.'/google-shopping.xml',
                $storageDir.'/google-merchant-ch.xml',
                $publicDir.'/google-shopping.xml',
                $publicDir.'/google-merchant-ch.xml',
            ]);

            $writer->writeTextAtomically($tsvBody, [
                $storageDir.'/google-merchant-ch.tsv',
                $publicDir.'/google-merchant-ch.tsv',
            ], $items);

            // Keep cache aligned with the file just written.
            $feed->forget();
            cache()->put('product_feed_google_xml', $xml, (int) config('feed.cache_ttl', 900));
        } catch (Throwable $e) {
            $this->error('Feed write blocked: '.$e->getMessage());
            $this->warn('Previous Merchant feed files were kept intact (anti 0-product protection).');

            return self::FAILURE;
        }

        $this->info(sprintf(
            'Feed OK — %d products (eligible=%d, unit_pricing=%d), xml=%d bytes',
            $items,
            $eligible,
            $withUnit,
            strlen($xml)
        ));
        $this->line('Public XML: '.url('/feeds/google-merchant-ch.xml'));
        $this->line('Public TSV: '.url('/feeds/google-merchant-ch.tsv'));
        $this->line('Register in Merchant Center: country=CH, language=German (de), currency=CHF.');

        return self::SUCCESS;
    }
}
