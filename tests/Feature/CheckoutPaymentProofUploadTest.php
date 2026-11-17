<?php

namespace Tests\Feature;

use App\Mail\PaymentProofSubmittedMail;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CheckoutPaymentProofUploadTest extends TestCase
{
    use RefreshDatabase;

    private function createOrder(): Order
    {
        $order = Order::query()->create([
            'first_name' => 'Anna',
            'last_name' => 'Muster',
            'email' => 'anna@example.ch',
            'address' => 'Bahnhofstrasse 1',
            'city' => 'Bern',
            'postcode' => '3000',
            'country' => 'Schweiz',
            'payment_method' => 'vorkasse',
            'reference' => 'HB-PROOF01',
            'status' => 'pending',
            'subtotal' => 80,
            'shipping' => 0,
            'total' => 80,
            'currency' => 'CHF',
        ]);

        OrderItem::query()->create([
            'order_id' => $order->id,
            'product_id' => null,
            'name' => 'Holzpellets',
            'price' => 80,
            'quantity' => 1,
            'line_total' => 80,
        ]);

        return $order;
    }

    public function test_confirmation_page_shows_payment_proof_upload(): void
    {
        $order = $this->createOrder();

        $this->get(route('checkout.confirmation', $order->reference))
            ->assertOk()
            ->assertSee('Zahlungsnachweis senden', false)
            ->assertSee('Datei auswählen', false)
            ->assertSee('Senden', false);
    }

    public function test_customer_can_upload_payment_proof_and_admin_is_notified(): void
    {
        Storage::fake('local');
        Mail::fake();

        $order = $this->createOrder();

        $this->post(route('checkout.payment-proof', $order->reference), [
            'payment_proof' => UploadedFile::fake()->create('beleg.pdf', 120, 'application/pdf'),
        ])
            ->assertRedirect(route('checkout.confirmation', $order->reference))
            ->assertSessionHas('status');

        $order->refresh();
        $this->assertNotEmpty($order->payment_proof_path);
        Storage::disk('local')->assertExists($order->payment_proof_path);

        Mail::assertSent(PaymentProofSubmittedMail::class, function (PaymentProofSubmittedMail $mail) use ($order) {
            return $mail->order->is($order)
                && count($mail->attachments()) === 1
                && str_contains($mail->attachments()[0]->as, 'Zahlungsnachweis-'.$order->reference);
        });
    }

    public function test_payment_proof_upload_requires_file(): void
    {
        $order = $this->createOrder();

        $this->from(route('checkout.confirmation', $order->reference))
            ->post(route('checkout.payment-proof', $order->reference), [])
            ->assertSessionHasErrors('payment_proof');
    }
}
