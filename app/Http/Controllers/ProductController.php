<?php

namespace App\Http\Controllers;

use App\Domain\Merchant\GoogleProductMapper;
use App\Models\Product;

class ProductController extends Controller
{
    public function __construct(private readonly GoogleProductMapper $mapper)
    {
    }

    public function show(Product $product)
    {
        $product->load('images', 'categories');

        $related = Product::with('images')
            ->whereHas('categories', fn ($q) => $q->whereIn('categories.id', $product->categories->pluck('id')))
            ->where('id', '!=', $product->id)
            ->inRandomOrder()
            ->take(4)
            ->get();

        $googleProduct = $this->mapper->map($product);

        return view('shop.show', compact('product', 'related', 'googleProduct'));
    }

    public function quickview(Product $product)
    {
        $product->load('images', 'categories');

        return view('partials.quickview', compact('product'));
    }
}
