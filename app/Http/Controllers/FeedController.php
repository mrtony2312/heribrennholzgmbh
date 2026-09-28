<?php

namespace App\Http\Controllers;

use App\Domain\Merchant\SafeFeedWriter;
use App\Services\ProductFeed;
use App\Services\ProductFeedTsv;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class FeedController extends Controller
{
    public function __construct(
        private readonly ProductFeed $feed,
        private readonly ProductFeedTsv $feedTsv,
        private readonly SafeFeedWriter $safeWriter,
    ) {
    }

    public function index(Request $request): Response
    {
        $this->guardToken($request);

        return response($this->xmlPayload(), 200, [
            'Content-Type' => 'application/xml; charset=UTF-8',
            'Cache-Control' => 'public, max-age=900',
            'X-Robots-Tag' => 'noindex',
        ]);
    }

    public function download(Request $request): Response
    {
        $this->guardToken($request);

        return response($this->xmlPayload(), 200, [
            'Content-Type' => 'application/xml; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="google-merchant-feed.xml"',
            'X-Robots-Tag' => 'noindex',
        ]);
    }

    public function tsv(Request $request): Response
    {
        $this->guardToken($request);

        return response($this->feedTsv->toTsv(), 200, [
            'Content-Type' => 'text/tab-separated-values; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="google-merchant-feed.tsv"',
            'X-Robots-Tag' => 'noindex',
        ]);
    }

    private function xmlPayload(): string
    {
        try {
            return $this->feed->toXml();
        } catch (Throwable $e) {
            Log::error('FeedController: generation failed, trying last-good', ['error' => $e->getMessage()]);
            $fallback = $this->safeWriter->loadLastGoodXml();
            if ($fallback !== null) {
                return $fallback;
            }
            throw $e;
        }
    }

    private function guardToken(Request $request): void
    {
        $token = (string) config('feed.token');
        if ($token !== '' && $request->query('token') !== $token) {
            throw new NotFoundHttpException;
        }
    }
}
