<?php

namespace App\Console\Commands\Merchant;

use App\Services\ProductFeed;
use App\Services\ProductFeedTsv;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class GenerateGoogleFeedCommand extends Command
{
    protected $signature = 'merchant:google-feed';

    protected $description = 'Generate the Google Merchant Center XML/TSV feeds';

    public function handle(ProductFeed $feed, ProductFeedTsv $tsv): int
    {
        $feed->forget();
        $tsv->forget();

        $xml = $feed->toXml();
        $dir = storage_path('app/feeds');
        File::ensureDirectoryExists($dir);
        File::put($dir.'/google-shopping.xml', $xml);
        File::put($dir.'/google-merchant-ch.xml', $xml);

        $publicDir = public_path('feeds');
        File::ensureDirectoryExists($publicDir);
        File::put($publicDir.'/google-shopping.xml', $xml);

        $this->info('Wrote '.$dir.'/google-shopping.xml ('.strlen($xml).' bytes)');

        return self::SUCCESS;
    }
}
