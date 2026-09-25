<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Collection;

class Wishlist
{
    private const KEY = 'wishlist';

    /** @return int[] */
    public function ids(): array
    {
        return array_values(session()->get(self::KEY, []));
    }

    public function has(int $productId): bool
    {
        return in_array($productId, $this->ids(), true);
    }

    public function toggle(int $productId): bool
    {
        $ids = $this->ids();
        if (in_array($productId, $ids, true)) {
            $ids = array_values(array_diff($ids, [$productId]));
            $added = false;
        } else {
            $ids[] = $productId;
            $added = true;
        }
        session()->put(self::KEY, $ids);

        return $added;
    }

    public function remove(int $productId): void
    {
        session()->put(self::KEY, array_values(array_diff($this->ids(), [$productId])));
    }

    public function count(): int
    {
        return count($this->ids());
    }

    /** @return Collection<int,Product> */
    public function items(): Collection
    {
        $ids = $this->ids();
        if (! $ids) {
            return collect();
        }

        return Product::with('images')->findMany($ids);
    }
}
