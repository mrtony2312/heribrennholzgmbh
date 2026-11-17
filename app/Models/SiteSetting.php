<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SiteSetting extends Model
{
    protected $guarded = [];

    public static function getValue(string $key, ?string $default = null): ?string
    {
        $value = Cache::rememberForever('site_setting.'.$key, function () use ($key) {
            return static::query()->where('key', $key)->value('value');
        });

        return $value !== null && $value !== '' ? (string) $value : $default;
    }

    public static function setValue(string $key, ?string $value): void
    {
        static::query()->updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );

        Cache::forget('site_setting.'.$key);
    }

    /** @return array{account_holder: string, bank_name: string, iban: string, bic: string, note: string} */
    public static function bankAccount(): array
    {
        return [
            'account_holder' => (string) static::getValue('bank_account_holder', config('shop.bank.account_holder', '')),
            'bank_name' => (string) static::getValue('bank_name', config('shop.bank.bank_name', '')),
            'iban' => (string) static::getValue('bank_iban', config('shop.bank.iban', '')),
            'bic' => (string) static::getValue('bank_bic', config('shop.bank.bic', '')),
            'note' => (string) static::getValue('bank_note', config('shop.bank.note', '')),
        ];
    }
}
