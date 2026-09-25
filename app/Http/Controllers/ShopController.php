<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function index(Request $request)
    {
        return $this->render($request, null);
    }

    public function category(Request $request, Category $category)
    {
        return $this->render($request, $category);
    }

    private function render(Request $request, ?Category $routeCategory)
    {
        $categories = Category::orderBy('position')->withCount('products')->get();

        // Active categories: explicit ?categories[] wins; otherwise the /categorie/{slug} route.
        if ($request->has('categories')) {
            $activeCategorySlugs = collect($request->input('categories', []))->filter()->unique()->values();
        } elseif ($routeCategory) {
            $activeCategorySlugs = collect([$routeCategory->slug]);
        } else {
            $activeCategorySlugs = collect();
        }

        $query = Product::query()->with(['images', 'categories']);

        if ($activeCategorySlugs->isNotEmpty()) {
            $query->whereHas('categories', fn ($q) => $q->whereIn('categories.slug', $activeCategorySlugs));
        }

        $search = trim((string) $request->get('q'));
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('short_description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('min')) {
            $query->where('price', '>=', (float) $request->get('min'));
        }
        if ($request->filled('max')) {
            $query->where('price', '<=', (float) $request->get('max'));
        }
        if ($request->boolean('in_stock')) {
            $query->where('in_stock', true);
        }
        if ($request->boolean('on_sale')) {
            $query->where('on_sale', true);
        }

        $sort = $request->get('sort', 'featured');
        match ($sort) {
            'a-z' => $query->orderBy('name'),
            'z-a' => $query->orderByDesc('name'),
            'price-asc' => $query->orderBy('price'),
            'price-desc' => $query->orderByDesc('price'),
            'newest' => $query->latest('id'),
            default => $query->orderByDesc('is_featured')->latest('id'),
        };

        $products = $query->paginate(24)->withQueryString();

        $priceBounds = [
            'min' => (int) floor((float) Product::min('price')),
            'max' => (int) ceil((float) Product::max('price')),
        ];

        // Titre : nom de la catégorie si une seule est active, sinon Recherche / Boutique
        if ($activeCategorySlugs->count() === 1) {
            $pageTitle = optional($categories->firstWhere('slug', $activeCategorySlugs->first()))->name ?? 'Shop';
        } elseif ($search !== '') {
            $pageTitle = 'Suche: ' . $search;
        } elseif ($activeCategorySlugs->count() > 1) {
            $pageTitle = 'Auswahl';
        } else {
            $pageTitle = 'Shop';
        }

        $data = [
            'products' => $products,
            'categories' => $categories,
            'search' => $search,
            'sort' => $sort,
            'activeCategorySlugs' => $activeCategorySlugs->all(),
            'priceBounds' => $priceBounds,
            'pageTitle' => $pageTitle,
        ];

        if ($request->ajax() || $request->wantsJson()) {
            return view('shop._results', $data);
        }

        return view('shop.index', $data);
    }
}
