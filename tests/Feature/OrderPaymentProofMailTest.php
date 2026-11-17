<?php

namespace Tests\Feature;

use App\Mail\NewOrderNotificationMail;
use App\Mail\OrderConfirmationMail;
use App\Models\Order;
use App\Models\Product;
use App\Models\SiteSetting;
use Database\Seeders\BankAccountSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class OrderPaymentProofMailTest extends TestCase
{
    use RefreshDatabase;

    public function test_order_emails_contain_customer_and_bank_details_without_proof_attachment(): void
    {
        Mail::fake();
        $this->seed(BankAccountSeeder::class);
        SiteSetting::setValue('bank_iban', 'CH9300762011623852957');
        SiteSetting::setValue('bank_account_holder', 'Heri Brennholz GmbH');

        $product = Product::factory()->create(['price' => 80, 'in_stock' => true]);

        $this->withSession(['cart' => [$product->id => 1]])
            ->post('/kasse', [
                'first_name' => 'Anna',
                'last_name' => 'Muster',
                'email' => 'anna@example.ch',
                'address' => 'Bahnhofstrasse 1',
                'city' => 'Bern',
                'postcode' => '3000',
                'payment_method' => 'vorkasse',
            ])
            ->assertRedirect();

        $order = Order::query()->firstOrFail();
        $this->assertNull($order->payment_proof_path);

        Mail::assertSent(OrderConfirmationMail::class, function (OrderConfirmationMail $mail) use ($order) {
            $html = $mail->render();

            return $mail->order->is($order)
                && count($mail->attachments()) === 0
                && str_contains($html, 'anna@example.ch')
                && str_contains($html, 'Bahnhofstrasse 1')
                && str_contains($html, 'Banküberweisung')
                && str_contains($html, 'CH93 0076 2011 6238 5295 7')
                && str_contains($html, 'Heri Brennholz GmbH');
        });

        Mail::assertSent(NewOrderNotificationMail::class, function (NewOrderNotificationMail $mail) use ($order) {
            $html = $mail->render();

            return $mail->order->is($order)
                && count($mail->attachments()) === 0
                && str_contains($html, 'Anna')
                && str_contains($html, 'Muster')
                && str_contains($html, 'anna@example.ch')
                && str_contains($html, 'Bahnhofstrasse 1')
                && str_contains($html, '3000')
                && str_contains($html, 'Bern')
                && str_contains($html, 'CH93 0076 2011 6238 5295 7');
        });
    }
}
