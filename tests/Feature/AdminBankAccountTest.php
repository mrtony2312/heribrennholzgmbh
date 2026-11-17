<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\SiteSetting;
use App\Models\User;
use Database\Seeders\BankAccountSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminBankAccountTest extends TestCase
{
    use RefreshDatabase;

    private function adminPath(): string
    {
        return '/'.trim((string) config('shop.admin_secret_path'), '/');
    }

    public function test_guest_is_redirected_from_bank_edit(): void
    {
        $this->get($this->adminPath().'/')
            ->assertRedirect(route('admin.login'));
    }

    public function test_admin_can_login_and_update_bank_account(): void
    {
        $this->seed(BankAccountSeeder::class);

        $user = User::factory()->create([
            'email' => 'admin@example.ch',
            'password' => 'secret-pass',
        ]);

        $this->post($this->adminPath().'/login', [
            'email' => $user->email,
            'password' => 'secret-pass',
        ])->assertRedirect(route('admin.bank.edit'));

        $this->actingAs($user)
            ->put($this->adminPath().'/bankkonto', [
                'account_holder' => 'Neue GmbH',
                'bank_name' => 'Test Bank',
                'iban' => 'CH44 3199 9123 0008 8901 2',
                'bic' => 'TESTCHZZ',
                'note' => 'Bitte Bestellnummer angeben.',
            ])
            ->assertRedirect();

        $bank = SiteSetting::bankAccount();
        $this->assertSame('Neue GmbH', $bank['account_holder']);
        $this->assertSame('CH4431999123000889012', $bank['iban']);
        $this->assertSame('TESTCHZZ', $bank['bic']);
        $this->assertSame('Test Bank', $bank['bank_name']);

        $order = Order::query()->create([
            'first_name' => 'Anna',
            'last_name' => 'Muster',
            'email' => 'anna@example.ch',
            'address' => 'Bahnhofstrasse 1',
            'city' => 'Bern',
            'postcode' => '3000',
            'country' => 'Schweiz',
            'payment_method' => 'vorkasse',
            'reference' => 'HB-ADMIN01',
            'status' => 'pending',
            'subtotal' => 50,
            'shipping' => 0,
            'total' => 50,
            'currency' => 'CHF',
        ]);

        OrderItem::query()->create([
            'order_id' => $order->id,
            'product_id' => null,
            'name' => 'Testprodukt',
            'price' => 50,
            'quantity' => 1,
            'line_total' => 50,
        ]);

        $this->get(route('checkout.confirmation', $order->reference))
            ->assertOk()
            ->assertSee('Neue GmbH', false)
            ->assertSee('Test Bank', false)
            ->assertSee('CH44 3199 9123 0008 8901 2', false)
            ->assertSee('TESTCHZZ', false);
    }

    public function test_login_page_is_reachable_on_secret_path(): void
    {
        $this->get($this->adminPath().'/login')
            ->assertOk()
            ->assertSee('Gestion du compte bancaire', false);
    }
}
