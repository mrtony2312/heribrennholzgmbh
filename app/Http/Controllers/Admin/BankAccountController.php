<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BankAccountController extends Controller
{
    public function edit(): View
    {
        return view('admin.bank-edit', [
            'bank' => SiteSetting::bankAccount(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'account_holder' => ['required', 'string', 'max:255'],
            'bank_name' => ['nullable', 'string', 'max:255'],
            'iban' => ['required', 'string', 'max:64'],
            'bic' => ['nullable', 'string', 'max:32'],
            'note' => ['nullable', 'string', 'max:1000'],
        ]);

        SiteSetting::setValue('bank_account_holder', $data['account_holder']);
        SiteSetting::setValue('bank_name', $data['bank_name'] ?? '');
        SiteSetting::setValue('bank_iban', preg_replace('/\s+/', '', $data['iban']) ?? $data['iban']);
        SiteSetting::setValue('bank_bic', $data['bic'] ?? '');
        SiteSetting::setValue('bank_note', $data['note'] ?? '');

        return back()->with('status', 'Bankkonto wurde aktualisiert. Die Kasse zeigt ab sofort die neuen Angaben.');
    }
}
