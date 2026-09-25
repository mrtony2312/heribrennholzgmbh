<?php

namespace App\Providers;

use App\Models\Category;
use App\Models\Product;
use App\Observers\ProductObserver;
use App\Services\Cart;
use App\Services\Wishlist;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(Cart::class);
        $this->app->singleton(Wishlist::class);
    }

    public function boot(): void
    {
        Paginator::useBootstrapFive();

        if ($this->app->environment('production') || str_starts_with((string) config('app.url'), 'https://')) {
            URL::forceScheme('https');
        }

        // Merchant-Center-Feed-Cache invalidieren, sobald ein Produkt sich ändert.
        Product::observe(ProductObserver::class);

        View::composer('*', function ($view) {
            $view->with('navCategories', Category::orderBy('position')->withCount('products')->get());
            $view->with('cart', app(Cart::class));
            $view->with('wishlist', app(Wishlist::class));
        });
    }
}
