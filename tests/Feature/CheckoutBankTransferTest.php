<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Product;
use App\Models\SiteSetting;
use Database\Seeders\BankAccountSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckoutBankTransferTest extends TestCase
{
    use RefreshDatabase;

    public function test_checkout_shows_only_bank_transfer_without_proof_upload(): void
    {
        $product = Product::factory()->create(['price' => 50, 'in_stock' => true]);

        $this->withSession(['cart' => [$product->id => 1]])
            ->get('/kasse')
            ->assertOk()
            ->assertSee('Banküberweisung', false)
            ->assertSee('Zahlungsart', false)
            ->assertDontSee('Zahlungsnachweis hochladen', false)
            ->assertDontSee('Datei auswählen', false)
            ->assertDontSee('Kauf auf Rechnung', false);
    }

    public function test_checkout_requires_payment_method_selection(): void
    {
        $product = Product::factory()->create(['price' => 50, 'in_stock' => true]);

        $this->withSession(['cart' => [$product->id => 1]])
            ->post('/kasse', [
                'first_name' => 'Anna',
                'last_name' => 'Muster',
                'email' => 'a@example.ch',
                'address' => 'Bahnhofstrasse 1',
                'city' => 'Bern',
                'postcode' => '3000',
            ])
            ->assertSessionHasErrors('payment_method');
    }

    public function test_order_confirmation_shows_bank_account_details(): void
    {
        $this->seed(BankAccountSeeder::class);
        SiteSetting::setValue('bank_iban', 'CH9300762011623852957');
        SiteSetting::setValue('bank_account_holder', 'Heri Brennholz GmbH');

        $product = Product::factory()->create(['price' => 50, 'in_stock' => true]);

        $response = $this->withSession(['cart' => [$product->id => 1]])
            ->post('/kasse', [
                'first_name' => 'Anna',
                'last_name' => 'Muster',
                'email' => 'a@example.ch',
                'address' => 'Bahnhofstrasse 1',
                'city' => 'Bern',
                'postcode' => '3000',
                'payment_method' => 'vorkasse',
            ]);

        $response->assertRedirect();
        $order = Order::query()->firstOrFail();
        $this->assertNull($order->payment_proof_path);

        $this->get(route('checkout.confirmation', $order->reference))
            ->assertOk()
            ->assertSee('Jetzt per Banküberweisung bezahlen', false)
            ->assertSee('CH93 0076 2011 6238 5295 7', false)
            ->assertSee('Heri Brennholz GmbH', false)
            ->assertSee($order->reference, false);
    }
}
