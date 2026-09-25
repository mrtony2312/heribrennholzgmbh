<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Collection;

class Cart
{
    private const KEY = 'cart';

    /** @return array<int,int> product_id => qty */
    public function raw(): array
    {
        return session()->get(self::KEY, []);
    }

    public function add(int $productId, int $qty = 1): void
    {
        $cart = $this->raw();
        $cart[$productId] = max(1, ($cart[$productId] ?? 0) + $qty);
        session()->put(self::KEY, $cart);
    }

    public function update(int $productId, int $qty): void
    {
        $cart = $this->raw();
        if ($qty <= 0) {
            unset($cart[$productId]);
        } else {
            $cart[$productId] = $qty;
        }
        session()->put(self::KEY, $cart);
    }

    public function remove(int $productId): void
    {
        $cart = $this->raw();
        unset($cart[$productId]);
        session()->put(self::KEY, $cart);
    }

    public function clear(): void
    {
        session()->forget(self::KEY);
    }

    /** @return Collection<int,array{product:Product,quantity:int,line_total:float}> */
    public function items(): Collection
    {
        $cart = $this->raw();
        if (empty($cart)) {
            return collect();
        }

        return Product::with('images')->findMany(array_keys($cart))->map(function (Product $p) use ($cart) {
            $qty = (int) $cart[$p->id];
            return [
                'product' => $p,
                'quantity' => $qty,
                'line_total' => round((float) $p->price * $qty, 2),
            ];
        })->values();
    }

    public function count(): int
    {
        return array_sum($this->raw());
    }

    public function subtotal(): float
    {
        return (float) $this->items()->sum('line_total');
    }

    public function shipping(): float
    {
        // Kostenlose Lieferung in der ganzen Schweiz.
        return 0.0;
    }

    public function total(): float
    {
        return round($this->subtotal() + $this->shipping(), 2);
    }
}
