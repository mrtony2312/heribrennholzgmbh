<?php

namespace App\Mail;

use App\Models\Order;
use App\Mail\Concerns\AddsPlainTextPart;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewOrderNotificationMail extends Mailable
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
            subject: 'Neue Bestellung ' . $this->order->reference . ' — ' . $this->order->money($this->order->total),
            replyTo: [new Address($this->order->email, $this->order->full_name)],
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.orders.admin');
    }
}
