<?php

namespace App\Http\Controllers;

use App\Services\ProductFeed;
use App\Services\ProductFeedTsv;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class FeedController extends Controller
{
    public function __construct(
        private readonly ProductFeed $feed,
        private readonly ProductFeedTsv $feedTsv,
    ) {
    }

    public function index(Request $request): Response
    {
        $this->guardToken($request);

        return response($this->feed->toXml(), 200, [
            'Content-Type' => 'application/xml; charset=UTF-8',
            'Cache-Control' => 'public, max-age=900',
            'X-Robots-Tag' => 'noindex',
        ]);
    }

    public function download(Request $request): Response
    {
        $this->guardToken($request);

        return response($this->feed->toXml(), 200, [
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

    private function guardToken(Request $request): void
    {
        $token = (string) config('feed.token');
        if ($token !== '' && $request->query('token') !== $token) {
            throw new NotFoundHttpException;
        }
    }
}
