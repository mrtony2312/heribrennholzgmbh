<?php

namespace App\Http\Controllers;

use App\Mail\NewOrderNotificationMail;
use App\Mail\OrderConfirmationMail;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\SiteSetting;
use App\Services\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

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

        return view('checkout.index', [
            'items' => $items,
            'bank' => SiteSetting::bankAccount(),
        ]);
    }

    public function store(Request $request)
    {
        $items = $this->cart->items();
        if ($items->isEmpty()) {
            return redirect()->route('cart.index')->with('status', 'Ihr Warenkorb ist leer.');
        }

        $outOfStock = $items->filter(fn ($item) => ! $item['product']->in_stock);
        if ($outOfStock->isNotEmpty()) {
            $names = $outOfStock->map(fn ($item) => $item['product']->name)->join(', ');

            return redirect()->route('cart.index')
                ->with('error', 'Folgende Artikel sind nicht mehr verfügbar: '.$names.'. Bitte entfernen Sie diese aus dem Warenkorb.');
        }

        $methods = array_keys(payment_methods());

        $data = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'address' => ['required', 'string', 'max:255'],
            'address_2' => ['nullable', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:255'],
            'postcode' => ['required', 'string', 'max:20'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'payment_method' => ['required', Rule::in($methods)],
            'payment_proof' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf,webp', 'max:8192'],
        ], [
            'payment_proof.required' => 'Bitte laden Sie einen Zahlungsnachweis hoch.',
            'payment_proof.file' => 'Der Zahlungsnachweis muss eine Datei sein.',
            'payment_proof.mimes' => 'Erlaubt sind JPG, PNG, WEBP oder PDF.',
            'payment_proof.max' => 'Der Zahlungsnachweis darf maximal 8 MB groß sein.',
        ]);

        $proofPath = $request->file('payment_proof')->store('payment-proofs', 'local');

        $order = Order::create([
            'first_name' => (string) ($data['first_name'] ?? ''),
            'last_name' => (string) ($data['last_name'] ?? ''),
            'email' => (string) ($data['email'] ?? ''),
            'phone' => $data['phone'] ?? null,
            'address' => (string) ($data['address'] ?? ''),
            'address_2' => $data['address_2'] ?? null,
            'city' => (string) ($data['city'] ?? ''),
            'postcode' => (string) ($data['postcode'] ?? ''),
            'country' => 'Schweiz',
            'notes' => $data['notes'] ?? null,
            'payment_method' => $data['payment_method'],
            'payment_proof_path' => $proofPath,
            'reference' => 'HB-'.strtoupper(Str::random(8)),
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

        return view('checkout.confirmation', [
            'order' => $order,
            'bank' => SiteSetting::bankAccount(),
        ]);
    }
}
