<?php

namespace Tests\Feature;

use App\Mail\NewOrderNotificationMail;
use App\Mail\OrderConfirmationMail;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class OrderPaymentProofMailTest extends TestCase
{
    use RefreshDatabase;

    public function test_payment_proof_is_attached_to_order_emails(): void
    {
        Storage::fake('local');
        Mail::fake();

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
                'payment_proof' => UploadedFile::fake()->create('beleg.pdf', 120, 'application/pdf'),
            ])
            ->assertRedirect();

        $order = Order::query()->firstOrFail();
        $this->assertNotEmpty($order->payment_proof_path);

        Mail::assertSent(OrderConfirmationMail::class, function (OrderConfirmationMail $mail) use ($order) {
            $html = $mail->render();

            return $mail->order->is($order)
                && count($mail->attachments()) === 1
                && str_contains($mail->attachments()[0]->as, 'Zahlungsnachweis-'.$order->reference)
                && str_contains($html, 'anna@example.ch')
                && str_contains($html, 'Bahnhofstrasse 1')
                && str_contains($html, 'Banküberweisung')
                && str_contains($html, 'Als Anhang');
        });

        Mail::assertSent(NewOrderNotificationMail::class, function (NewOrderNotificationMail $mail) use ($order) {
            $html = $mail->render();

            return $mail->order->is($order)
                && count($mail->attachments()) === 1
                && str_contains($mail->attachments()[0]->as, 'Zahlungsnachweis-'.$order->reference)
                && str_contains($html, 'Anna')
                && str_contains($html, 'Muster')
                && str_contains($html, 'anna@example.ch')
                && str_contains($html, 'Bahnhofstrasse 1')
                && str_contains($html, '3000')
                && str_contains($html, 'Bern')
                && str_contains($html, 'Als Anhang dieser E-Mail beigefügt');
        });
    }
}
