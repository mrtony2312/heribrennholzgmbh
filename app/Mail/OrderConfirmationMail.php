<?php

namespace App\Mail;

use App\Models\Order;
use App\Mail\Concerns\AddsPlainTextPart;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderConfirmationMail extends Mailable
{
    use AddsPlainTextPart;
    use Queueable;
    use SerializesModels;

    public function __construct(public Order $order)
    {
        $this->order->loadMissing('items');
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            using: [$this->plainTextPart()],
            subject: 'Ihre Bestellung ' . $this->order->reference . ' bei ' . config('app.name'),
            replyTo: [config('mail.admin.address')],
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.orders.customer');
    }
}
