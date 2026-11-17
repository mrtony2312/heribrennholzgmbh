<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\SiteSetting;
use Database\Seeders\BankAccountSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CheckoutBankTransferTest extends TestCase
{
    use RefreshDatabase;

    public function test_checkout_shows_only_bank_transfer_and_account_details(): void
    {
        $this->seed(BankAccountSeeder::class);
        SiteSetting::setValue('bank_iban', 'CH9300762011623852957');
        SiteSetting::setValue('bank_account_holder', 'Heri Brennholz GmbH');

        $product = Product::factory()->create(['price' => 50, 'in_stock' => true]);

        $this->withSession(['cart' => [$product->id => 1]])
            ->get('/kasse')
            ->assertOk()
            ->assertSee('Banküberweisung', false)
            ->assertSee('CH93 0076 2011 6238 5295 7', false)
            ->assertSee('Heri Brennholz GmbH', false)
            ->assertSee('Zahlungsnachweis hochladen', false)
            ->assertDontSee('Kauf auf Rechnung', false);
    }

    public function test_checkout_requires_payment_proof(): void
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
                'payment_method' => 'vorkasse',
            ])
            ->assertSessionHasErrors('payment_proof');
    }

    public function test_checkout_accepts_payment_proof_upload(): void
    {
        Storage::fake('local');
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
                'payment_proof' => UploadedFile::fake()->image('beleg.jpg'),
            ]);

        $response->assertRedirect();
        $this->assertDatabaseCount('orders', 1);
    }
}
