<?php

namespace App\Domain\Merchant;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use RuntimeException;

/**
 * Writes Merchant feeds safely so a bad/empty generation cannot wipe a good feed
 * (which would make Google Merchant Center drop all products on the next fetch).
 */
class SafeFeedWriter
{
    public function __construct(
        private readonly GoogleFeedGenerator $generator,
    ) {
    }

    /**
     * Build XML and refuse empty / catastrophic drops vs the previous good file.
     *
     * @return array{xml: string, items: int, wrote: bool, path: string}
     */
    public function buildXml(): array
    {
        $xml = $this->generator->toXml();
        $items = $this->countItems($xml);

        if ($items < 1) {
            throw new RuntimeException('Refusing empty Google Merchant feed (0 <item>). Catalog mapping produced no products.');
        }

        $minAbsolute = (int) config('feed.min_items', 1);
        if ($items < $minAbsolute) {
            throw new RuntimeException("Refusing Google Merchant feed: {$items} items < min_items={$minAbsolute}.");
        }

        return [
            'xml' => $xml,
            'items' => $items,
            'wrote' => false,
            'path' => '',
        ];
    }

    /**
     * Persist XML/TSV to disk with atomic replace + last-good backup.
     *
     * @param  list<string>  $paths
     * @return array{items: int, paths: list<string>}
     */
    public function writeXmlAtomically(string $xml, array $paths): array
    {
        $items = $this->countItems($xml);
        if ($items < 1) {
            throw new RuntimeException('Refusing to write empty Merchant XML.');
        }

        $written = [];
        foreach ($paths as $path) {
            $this->assertNotCatastrophicDrop($path, $items);
            $this->atomicPut($path, $xml);
            $written[] = $path;

            $backup = $path.'.last-good';
            File::put($backup, $xml);
        }

        return ['items' => $items, 'paths' => $written];
    }

    /**
     * @param  list<string>  $paths
     */
    public function writeTextAtomically(string $body, array $paths, int $expectedItems): void
    {
        if ($expectedItems < 1 || trim($body) === '') {
            throw new RuntimeException('Refusing to write empty Merchant TSV.');
        }

        foreach ($paths as $path) {
            $this->atomicPut($path, $body);
            File::put($path.'.last-good', $body);
        }
    }

    public function countItems(string $xml): int
    {
        return substr_count($xml, '<item>');
    }

    public function loadLastGoodXml(?string $preferredPath = null): ?string
    {
        $candidates = array_filter([
            $preferredPath ? $preferredPath.'.last-good' : null,
            public_path('feeds/google-merchant-ch.xml.last-good'),
            storage_path('app/feeds/google-merchant-ch.xml.last-good'),
            public_path('feeds/google-merchant-ch.xml'),
            storage_path('app/feeds/google-merchant-ch.xml'),
        ]);

        foreach ($candidates as $path) {
            if (is_file($path)) {
                $xml = (string) file_get_contents($path);
                if ($this->countItems($xml) > 0) {
                    return $xml;
                }
            }
        }

        return null;
    }

    private function assertNotCatastrophicDrop(string $path, int $newItems): void
    {
        if (! is_file($path)) {
            return;
        }

        $previous = $this->countItems((string) file_get_contents($path));
        if ($previous < 1) {
            return;
        }

        // Block writes that would wipe most of the catalog (e.g. DB empty / mapping bug).
        $floor = max(10, (int) floor($previous * 0.5));
        if ($newItems < $floor) {
            Log::error('Blocked catastrophic Merchant feed drop', [
                'path' => $path,
                'previous_items' => $previous,
                'new_items' => $newItems,
                'floor' => $floor,
            ]);

            throw new RuntimeException(
                "Refusing Merchant feed write: {$newItems} items would replace {$previous} (floor {$floor})."
            );
        }
    }

    private function atomicPut(string $path, string $contents): void
    {
        File::ensureDirectoryExists(dirname($path));
        $tmp = $path.'.tmp.'.getmypid().'.'.bin2hex(random_bytes(4));
        File::put($tmp, $contents);

        // Windows-safe replace
        if (is_file($path)) {
            @unlink($path);
        }
        if (! @rename($tmp, $path)) {
            File::move($tmp, $path);
        }
    }
}
