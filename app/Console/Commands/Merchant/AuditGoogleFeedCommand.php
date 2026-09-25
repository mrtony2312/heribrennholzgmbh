<?php

namespace App\Console\Commands\Merchant;

use App\Domain\Merchant\GoogleFeedValidator;
use App\Domain\Merchant\GoogleProductMapper;
use App\Models\Product;
use App\Services\ProductFeed;
use Illuminate\Console\Command;
use SimpleXMLElement;

class AuditGoogleFeedCommand extends Command
{
    protected $signature = 'merchant:google-feed-audit {--all : Audit every eligible product} {--limit=20}';

    protected $description = 'Compare mapper, XML feed and PDP HTML for price/stock mismatches';

    public function handle(ProductFeed $feed, GoogleProductMapper $mapper, GoogleFeedValidator $validator): int
    {
        $xml = $feed->toXml();
        $sx = new SimpleXMLElement($xml);
        $sx->registerXPathNamespace('g', 'http://base.google.com/ns/1.0');

        $itemsById = [];
        foreach ($sx->channel->item as $item) {
            $g = $item->children('http://base.google.com/ns/1.0');
            $itemsById[(string) $g->id] = $item;
        }

        $query = Product::with(['images', 'categories'])->orderBy('id');
        if (! $this->option('all')) {
            $query->limit((int) $this->option('limit'));
        }

        $failed = 0;
        $checked = 0;

        foreach ($query->get() as $product) {
            $dto = $mapper->map($product);
            if (! $dto) {
                continue;
            }
            $checked++;
            $errors = $validator->validateDto($dto);

            if (isset($itemsById[$dto->id])) {
                $errors = array_merge($errors, $validator->compareXmlItem($itemsById[$dto->id], $dto));
            } else {
                $errors[] = 'missing from XML';
            }

            $kernel = $this->laravel->make(\Illuminate\Contracts\Http\Kernel::class);
            $request = \Illuminate\Http\Request::create($dto->link, 'GET');
            $response = $kernel->handle($request);
            if ($response->getStatusCode() !== 200) {
                $errors[] = 'PDP HTTP '.$response->getStatusCode();
            } else {
                $errors = array_merge($errors, $validator->compareLandingHtml($response->getContent(), $dto));
            }

            if ($errors) {
                $failed++;
                $this->error('#'.$dto->id.' '.$dto->title.': '.implode('; ', $errors));
            }
        }

        $this->info("Checked {$checked} products, {$failed} mismatch(es).");

        return $failed > 0 ? self::FAILURE : self::SUCCESS;
    }
}
