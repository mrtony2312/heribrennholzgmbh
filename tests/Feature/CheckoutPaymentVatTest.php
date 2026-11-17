<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CheckoutPaymentVatTest extends TestCase
{
    use RefreshDatabase;

    public function test_vat_is_extracted_from_gross_price(): void
    {
        $this->assertSame(8.10, vat_included(108.10));
    }

    public function test_only_enabled_payment_methods_are_offered(): void
    {
        $this->assertSame(['vorkasse'], array_keys(payment_methods()));
    }

    public function test_pages_show_vat_and_payment_methods(): void
    {
        foreach (['/agb', '/faq', '/warenkorb'] as $url) {
            $this->get($url)->assertOk()->assertSee('8.1 %', false);
        }
        $this->get('/faq')->assertSee('Banküberweisung', false)->assertDontSee('TWINT', false);
    }

    public function test_checkout_stores_payment_method_and_rejects_inactive_ones(): void
    {
        Storage::fake('local');

        $product = Product::factory()->create(['price' => 100]);
        $data = [
            'first_name' => 'Anna', 'last_name' => 'Muster', 'email' => 'a@example.ch',
            'address' => 'Bahnhofstrasse 1', 'city' => 'Bern', 'postcode' => '3000', 'country' => 'Schweiz',
            'payment_proof' => UploadedFile::fake()->create('beleg.pdf', 100, 'application/pdf'),
        ];

        $this->withSession(['cart' => [$product->id => 1]])
            ->post('/kasse', $data + ['payment_method' => 'twint'])
            ->assertSessionHasErrors('payment_method');

        $this->withSession(['cart' => [$product->id => 1]])
            ->post('/kasse', $data + ['payment_method' => 'vorkasse'])
            ->assertRedirect();

        $order = Order::first();
        $this->assertSame('vorkasse', $order->payment_method);
        $this->assertNotEmpty($order->payment_proof_path);
        Storage::disk('local')->assertExists($order->payment_proof_path);
    }
}
