<?php

namespace App\Http\Controllers;

use App\Mail\NewOrderNotificationMail;
use App\Mail\OrderConfirmationMail;
use App\Models\Order;
use App\Models\OrderItem;
use App\Services\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    public function __construct(private Cart $cart)
    {
    }

    public function index()
    {
        $items = $this->cart->items();
        if ($items->isEmpty()) {
            return redirect()->route('cart.index')->with('status', 'Ihr Warenkorb ist leer.');
        }

        return view('checkout.index', ['items' => $items]);
    }

    public function store(Request $request)
    {
        $items = $this->cart->items();
        if ($items->isEmpty()) {
            return redirect()->route('cart.index')->with('status', 'Ihr Warenkorb ist leer.');
        }

        // Keine Validierung: Felder werden unverändert übernommen, fehlende Pflichtfelder der DB als leer gespeichert.
        $data = [];
        foreach (['first_name', 'last_name', 'email', 'address', 'city', 'postcode'] as $field) {
            $data[$field] = (string) $request->input($field, '');
        }
        foreach (['phone', 'address_2', 'notes'] as $field) {
            $data[$field] = $request->input($field);
        }
        $data['country'] = 'Schweiz';
        $data['payment_method'] = $request->input('payment_method') ?: array_key_first(payment_methods());

        $order = Order::create([
            ...$data,
            'reference' => 'HB-' . strtoupper(Str::random(8)),
            'status' => 'pending',
            'subtotal' => $this->cart->subtotal(),
            'shipping' => $this->cart->shipping(),
            'total' => $this->cart->total(),
            'currency' => 'CHF',
        ]);

        foreach ($items as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item['product']->id,
                'name' => $item['product']->name,
                'price' => $item['product']->price,
                'quantity' => $item['quantity'],
                'line_total' => $item['line_total'],
            ]);
        }

        $this->cart->clear();

        $order->load('items');
        $this->sendOrderMails($order);

        return redirect()->route('checkout.confirmation', $order->reference);
    }

    /**
     * Bestätigung an den Kunden + Benachrichtigung an die Bestellannahme.
     * Ein Mailfehler darf den Bestellabschluss nicht verhindern.
     */
    private function sendOrderMails(Order $order): void
    {
        try {
            Mail::to($order->email, $order->full_name)
                ->send(new OrderConfirmationMail($order));
        } catch (\Throwable $e) {
            Log::error('Bestellbestätigung an Kunden fehlgeschlagen', [
                'order' => $order->reference, 'error' => $e->getMessage(),
            ]);
        }

        try {
            Mail::to(config('mail.admin.address'), config('mail.admin.name'))
                ->send(new NewOrderNotificationMail($order));
        } catch (\Throwable $e) {
            Log::error('Bestellbenachrichtigung an Admin fehlgeschlagen', [
                'order' => $order->reference, 'error' => $e->getMessage(),
            ]);
        }
    }

    public function confirmation(Order $order)
    {
        $order->load('items');

        return view('checkout.confirmation', compact('order'));
    }
}
