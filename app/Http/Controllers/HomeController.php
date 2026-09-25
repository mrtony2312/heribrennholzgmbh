<?php

namespace App\Http\Controllers;

use App\Models\Category;

class HomeController extends Controller
{
    public function index()
    {
        $categories = Category::orderBy('position')
            ->withCount('products')
            ->with(['products' => fn ($q) => $q->with('images')->latest('products.id')->limit(4)])
            ->get();

        // Themed image for each category: Pexels site image if present, else first product image.
        $catImages = [];
        foreach ($categories as $cat) {
            if (is_file(public_path("media/site/cat-{$cat->slug}.jpg"))) {
                $catImages[$cat->slug] = asset("media/site/cat-{$cat->slug}.jpg");
            } else {
                $catImages[$cat->slug] = optional(optional($cat->products->first())->images->first())->url
                    ?? asset('assets/images/item/item-bg.jpg');
            }
        }

        $heroImages = [];
        foreach (['hero-1', 'hero-2', 'hero-3', 'cta-1'] as $name) {
            $heroImages[$name] = is_file(public_path("media/site/{$name}.jpg"))
                ? asset("media/site/{$name}.jpg")
                : null;
        }

        return view('home', compact('categories', 'catImages', 'heroImages'));
    }
}
