<?php

namespace App\Domain\Merchant\Api;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use RuntimeException;

/**
 * Merchant API REST client (products/v1).
 * Auth (same as Naturalenha / Casacuberta): OAuth refresh token OR service-account JSON.
 */
class MerchantApiClient
{
    private const SCOPE = 'https://www.googleapis.com/auth/content';

    private const TOKEN_URL = 'https://oauth2.googleapis.com/token';

    private const BASE = 'https://merchantapi.googleapis.com';

    private string $accountId;

    private string $dataSourceId;

    private ?string $credentialsPath;

    private int $timeout;

    public function __construct(
        ?string $accountId = null,
        ?string $dataSourceId = null,
        ?string $credentialsPath = null,
        ?int $timeout = null,
    ) {
        $this->accountId = $accountId ?? (string) config('merchant.api.account_id', '');
        $this->dataSourceId = $dataSourceId ?? (string) config('merchant.api.data_source_id', '');
        $configured = $credentialsPath ?? (string) config('merchant.api.credentials', 'google/merchant-sa.json');
        $this->credentialsPath = $this->resolveCredentialsPath($configured);
        $this->timeout = $timeout ?? (int) config('merchant.api.timeout', 30);
    }

    public function oauthConfigured(): bool
    {
        return filled(config('merchant.oauth.client_id'))
            && filled(config('merchant.oauth.client_secret'))
            && filled(config('merchant.oauth.refresh_token'));
    }

    public function serviceAccountConfigured(): bool
    {
        return $this->credentialsPath !== null && is_readable($this->credentialsPath);
    }

    public function authMode(): string
    {
        if ($this->oauthConfigured()) {
            return 'oauth';
        }

        if ($this->serviceAccountConfigured()) {
            return 'service_account';
        }

        return 'none';
    }

    public function isConfigured(): bool
    {
        return $this->accountId !== ''
            && ($this->oauthConfigured() || $this->serviceAccountConfigured());
    }

    /**
     * @return array<string, mixed>
     */
    public function status(): array
    {
        $email = null;
        if ($this->serviceAccountConfigured()) {
            $json = json_decode((string) file_get_contents((string) $this->credentialsPath), true);
            $email = is_array($json) ? ($json['client_email'] ?? null) : null;
        }

        return [
            'account_id' => $this->accountId,
            'data_source_id' => $this->dataSourceId,
            'data_source' => $this->dataSourceId !== '' ? $this->dataSourceName() : '',
            'credentials' => $this->credentialsPath ?? '',
            'email' => $email,
            'auth_mode' => $this->authMode(),
            'oauth_client_id' => filled(config('merchant.oauth.client_id')) ? 'set' : 'missing',
            'oauth_secret' => filled(config('merchant.oauth.client_secret')) ? 'set' : 'missing',
            'oauth_refresh' => filled(config('merchant.oauth.refresh_token')) ? 'set' : 'missing',
            'configured' => $this->isConfigured(),
            'ready_to_sync' => $this->isConfigured() && $this->dataSourceId !== '',
            'target_country' => (string) config('merchant.target_country', 'CH'),
            'content_language' => (string) config('merchant.content_language', 'de'),
            'feed_label' => (string) config('merchant.feed_label', 'CH'),
            'currency' => (string) config('merchant.currency', 'CHF'),
        ];
    }

    public function dataSourceName(): string
    {
        return sprintf('accounts/%s/dataSources/%s', $this->accountId, $this->dataSourceId);
    }

    public function parent(): string
    {
        return sprintf('accounts/%s', $this->accountId);
    }

    /**
     * @param  array<string, mixed>  $productInput
     * @return array<string, mixed>
     */
    public function insertProductInput(array $productInput): array
    {
        $this->assertConfigured(requireDataSource: true);

        $url = self::BASE.'/products/v1/'.$this->parent().'/productInputs:insert';

        $response = $this->http()
            ->withQueryParameters(['dataSource' => $this->dataSourceName()])
            ->post($url, $productInput);

        if ($response->failed()) {
            throw new RuntimeException(sprintf(
                'Merchant API insert failed (%s): %s',
                $response->status(),
                $response->body()
            ));
        }

        /** @var array<string, mixed> */
        return $response->json() ?? [];
    }

    /**
     * @return array<string, mixed>
     */
    public function getAccount(): array
    {
        $this->assertConfigured(requireDataSource: false);

        $url = self::BASE.'/accounts/v1/'.$this->parent();
        $response = $this->http()->get($url);

        if ($response->failed()) {
            throw new RuntimeException(sprintf(
                'Merchant API accounts.get failed (%s): %s',
                $response->status(),
                $response->body()
            ));
        }

        /** @var array<string, mixed> */
        return $response->json() ?? [];
    }

    public function accessToken(): string
    {
        $this->assertConfigured(requireDataSource: false);

        $cacheKey = 'merchant.google.access_token.'.$this->authMode();
        $cached = Cache::get($cacheKey);
        if (is_string($cached) && $cached !== '') {
            return $cached;
        }

        return $this->oauthConfigured()
            ? $this->fetchOAuthAccessToken()
            : $this->fetchServiceAccountAccessToken();
    }

    private function fetchOAuthAccessToken(): string
    {
        $response = Http::asForm()
            ->timeout($this->timeout)
            ->post(self::TOKEN_URL, [
                'grant_type' => 'refresh_token',
                'client_id' => (string) config('merchant.oauth.client_id'),
                'client_secret' => (string) config('merchant.oauth.client_secret'),
                'refresh_token' => (string) config('merchant.oauth.refresh_token'),
            ]);

        if ($response->failed()) {
            throw new RuntimeException(
                'OAuth token refresh failed (HTTP '.$response->status().'). Check GOOGLE_CLIENT_ID / SECRET / REFRESH_TOKEN.'
            );
        }

        $json = $response->json();
        $token = $json['access_token'] ?? null;
        $expires = (int) ($json['expires_in'] ?? 3600);

        if (! is_string($token) || $token === '') {
            throw new RuntimeException('Google OAuth did not return an access token.');
        }

        Cache::put('merchant.google.access_token.oauth', $token, max(60, $expires - 60));

        return $token;
    }

    private function fetchServiceAccountAccessToken(): string
    {
        $class = 'Google\\Auth\\Credentials\\ServiceAccountCredentials';
        if (! class_exists($class)) {
            throw new RuntimeException(
                'Service-account auth requires google/auth. Run: composer require google/auth'
            );
        }

        $credentials = new $class(self::SCOPE, $this->credentialsPath);
        $token = $credentials->fetchAuthToken();

        if (empty($token['access_token'])) {
            throw new RuntimeException('Unable to obtain Google OAuth access token from service account.');
        }

        $access = (string) $token['access_token'];
        $expires = (int) ($token['expires_in'] ?? 3600);
        Cache::put('merchant.google.access_token.service_account', $access, max(60, $expires - 60));

        return $access;
    }

    private function http(): PendingRequest
    {
        return Http::withToken($this->accessToken())
            ->acceptJson()
            ->asJson()
            ->timeout($this->timeout);
    }

    private function assertConfigured(bool $requireDataSource): void
    {
        if ($this->accountId === '') {
            throw new RuntimeException('Set MERCHANT_ACCOUNT_ID (Merchant Center account id).');
        }

        if (! $this->oauthConfigured() && ! $this->serviceAccountConfigured()) {
            throw new RuntimeException(
                'Missing auth. Set GOOGLE_CLIENT_ID + GOOGLE_CLIENT_SECRET + GOOGLE_REFRESH_TOKEN '
                .'(comme Naturalenha) OR MERCHANT_API_CREDENTIALS (service-account JSON).'
            );
        }

        if ($requireDataSource && $this->dataSourceId === '') {
            throw new RuntimeException('Set MERCHANT_DATA_SOURCE_ID (API primary data source).');
        }
    }

    private function resolveCredentialsPath(string $path): ?string
    {
        if ($path === '') {
            return null;
        }

        if (is_file($path)) {
            return realpath($path) ?: $path;
        }

        $fromStorage = storage_path('app/'.ltrim(str_replace('\\', '/', $path), '/'));

        if (is_file($fromStorage)) {
            return realpath($fromStorage) ?: $fromStorage;
        }

        return null;
    }
}
