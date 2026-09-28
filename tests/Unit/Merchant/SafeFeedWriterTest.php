<?php

namespace Tests\Unit\Merchant;

use App\Domain\Merchant\SafeFeedWriter;
use Illuminate\Support\Facades\File;
use RuntimeException;
use Tests\TestCase;

class SafeFeedWriterTest extends TestCase
{
    public function test_refuses_catastrophic_drop(): void
    {
        $dir = storage_path('app/feeds-test-'.uniqid());
        File::ensureDirectoryExists($dir);
        $path = $dir.'/feed.xml';

        $good = '<?xml version="1.0"?><rss><channel>'
            .str_repeat('<item><g:id>1</g:id></item>', 40)
            .'</channel></rss>';
        File::put($path, $good);

        $writer = app(SafeFeedWriter::class);
        $tiny = '<?xml version="1.0"?><rss><channel><item><g:id>1</g:id></item></channel></rss>';

        try {
            $this->expectException(RuntimeException::class);
            $writer->writeXmlAtomically($tiny, [$path]);
        } finally {
            File::deleteDirectory($dir);
        }
    }

    public function test_refuses_empty_xml(): void
    {
        $writer = app(SafeFeedWriter::class);
        $empty = '<?xml version="1.0"?><rss><channel></channel></rss>';

        $this->expectException(RuntimeException::class);
        $writer->writeXmlAtomically($empty, [storage_path('app/feeds/should-not-exist-'.uniqid().'.xml')]);
    }
}
