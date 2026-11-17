<?php

namespace Database\Seeders;

use App\Models\SiteSetting;
use Illuminate\Database\Seeder;

class BankAccountSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            'bank_account_holder' => config('shop.bank.account_holder'),
            'bank_name' => config('shop.bank.bank_name'),
            'bank_iban' => config('shop.bank.iban'),
            'bank_bic' => config('shop.bank.bic'),
            'bank_note' => config('shop.bank.note'),
        ];

        foreach ($defaults as $key => $value) {
            if (SiteSetting::query()->where('key', $key)->exists()) {
                continue;
            }

            SiteSetting::setValue($key, (string) $value);
        }
    }
}
