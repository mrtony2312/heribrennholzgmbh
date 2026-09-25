<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\Cart;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function __construct(private Cart $cart)
    {
    }

    public function index()
    {
        return view('cart.index', ['items' => $this->cart->items()]);
    }

    public function drawer()
    {
        return view('partials.cart-drawer-body');
    }

    public function add(Request $request)
    {
        $data = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'quantity' => ['nullable', 'integer', 'min:1', 'max:99'],
        ]);

        $this->cart->add((int) $data['product_id'], (int) ($data['quantity'] ?? 1));

        $product = Product::find($data['product_id']);
        $message = $product->name . ' wurde in den Warenkorb gelegt.';

        if ($request->wantsJson()) {
            return response()->json([
                'message' => $message,
                'count' => $this->cart->count(),
                'subtotal' => $this->cart->subtotal(),
            ]);
        }

        return back()->with('status', $message);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'product_id' => ['required', 'integer'],
            'quantity' => ['required', 'integer', 'min:0', 'max:99'],
        ]);

        $productId = (int) $data['product_id'];
        $qty = (int) $data['quantity'];
        $this->cart->update($productId, $qty);

        if ($request->wantsJson()) {
            return response()->json($this->summary([
                'product_id' => $productId,
                'quantity' => $qty,
                'removed' => $qty <= 0,
                'line_total' => $qty <= 0 ? 0.0 : round((float) optional(Product::find($productId))->price * $qty, 2),
            ]));
        }

        return back()->with('status', 'Warenkorb aktualisiert.');
    }

    public function remove(Request $request)
    {
        $productId = (int) $request->input('product_id');
        $this->cart->remove($productId);

        if ($request->wantsJson()) {
            return response()->json($this->summary([
                'product_id' => $productId,
                'removed' => true,
            ]));
        }

        return back()->with('status', 'Artikel aus dem Warenkorb entfernt.');
    }

    /** Aktuelle Warenkorb-Kennzahlen für AJAX-Antworten. */
    private function summary(array $extra = []): array
    {
        return array_merge([
            'count' => $this->cart->count(),
            'subtotal' => $this->cart->subtotal(),
            'shipping' => $this->cart->shipping(),
            'total' => $this->cart->total(),
            'empty' => $this->cart->count() === 0,
        ], $extra);
    }
}
