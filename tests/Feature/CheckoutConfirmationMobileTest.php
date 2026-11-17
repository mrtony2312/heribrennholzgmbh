<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\SiteSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckoutConfirmationMobileTest extends TestCase
{
    use RefreshDatabase;

    public function test_confirmation_page_renders_mobile_friendly_layout(): void
    {
        SiteSetting::setValue('bank_account_holder', 'Heri Brennholz GmbH');
        SiteSetting::setValue('bank_iban', 'CH9300762011623852957');

        $order = Order::query()->create([
            'first_name' => 'Anna',
            'last_name' => 'Muster',
            'email' => 'anna@example.ch',
            'address' => 'Bahnhofstrasse 1',
            'city' => 'Bern',
            'postcode' => '3000',
            'country' => 'Schweiz',
            'payment_method' => 'vorkasse',
            'payment_proof_path' => 'payment-proofs/demo.jpg',
            'reference' => 'HB-MOBILE01',
            'status' => 'pending',
            'subtotal' => 100,
            'shipping' => 0,
            'total' => 100,
            'currency' => 'CHF',
        ]);

        OrderItem::query()->create([
            'order_id' => $order->id,
            'product_id' => null,
            'name' => 'Holzpellets Premium ENplus A1 15 kg',
            'price' => 100,
            'quantity' => 1,
            'line_total' => 100,
        ]);

        $this->get(route('checkout.confirmation', $order->reference))
            ->assertOk()
            ->assertSee('confirmation-page', false)
            ->assertSee('confirmation-card', false)
            ->assertSee('HB-MOBILE01', false)
            ->assertSee('Holzpellets Premium ENplus A1 15 kg', false)
            ->assertSee('CH93 0076 2011 6238 5295 7', false)
            ->assertSee('Zurück zum Shop', false);
    }
}
