<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\FeedController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ShopController;

use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/shop', [ShopController::class, 'index'])->name('shop.index');
Route::get('/suche', [ShopController::class, 'index'])->name('shop.search');

Route::redirect('/kategorie/brennholz', '/kategorie/scheitholz', 301);
Route::redirect('/kategorie/kaminholz', '/kategorie/scheitholz', 301);

Route::get('/kategorie/{category:slug}', [ShopController::class, 'category'])->name('shop.category');
Route::get('/produkt/{product:slug}', [ProductController::class, 'show'])->name('product.show');
Route::get('/produkt/{product:slug}/vorschau', [ProductController::class, 'quickview'])->name('product.quickview');

Route::get('/favoriten', [\App\Http\Controllers\WishlistController::class, 'index'])->name('wishlist.index');
Route::post('/favoriten', [\App\Http\Controllers\WishlistController::class, 'toggle'])->name('wishlist.toggle');
Route::delete('/favoriten', [\App\Http\Controllers\WishlistController::class, 'remove'])->name('wishlist.remove');

Route::get('/warenkorb', [CartController::class, 'index'])->name('cart.index');
Route::get('/warenkorb/inhalt', [CartController::class, 'drawer'])->name('cart.drawer');
Route::post('/warenkorb/hinzufuegen', [CartController::class, 'add'])->name('cart.add');
Route::patch('/warenkorb', [CartController::class, 'update'])->name('cart.update');
Route::delete('/warenkorb', [CartController::class, 'remove'])->name('cart.remove');

Route::get('/kasse', [CheckoutController::class, 'index'])->name('checkout.index');
Route::post('/kasse', [CheckoutController::class, 'store'])->name('checkout.store');
Route::get('/kasse/bestaetigung/{order:reference}', [CheckoutController::class, 'confirmation'])->name('checkout.confirmation');

Route::get('/ueber-uns', [PageController::class, 'about'])->name('pages.about');
Route::get('/kontakt', [PageController::class, 'contact'])->name('pages.contact');
Route::post('/kontakt', [PageController::class, 'contactSubmit'])->name('pages.contact.submit');
Route::get('/faq', [PageController::class, 'faq'])->name('pages.faq');
Route::get('/datenschutz', [PageController::class, 'privacy'])->name('pages.privacy');
Route::get('/agb', [PageController::class, 'terms'])->name('pages.terms');
Route::get('/impressum', [PageController::class, 'impressum'])->name('pages.impressum');
Route::get('/versand', [PageController::class, 'versand'])->name('pages.versand');
Route::get('/rueckgabe', [PageController::class, 'rueckgabe'])->name('pages.rueckgabe');
Route::get('/sitemap.xml', [PageController::class, 'sitemap'])->name('sitemap');

Route::redirect('/contact', '/kontakt', 301);
Route::redirect('/a-propos', '/ueber-uns', 301);
Route::redirect('/livraison', '/versand', 301);
Route::redirect('/retours', '/rueckgabe', 301);
Route::redirect('/confidentialite', '/datenschutz', 301);
Route::redirect('/cgv', '/agb', 301);

Route::get('/feeds/google-merchant-ch.xml', [FeedController::class, 'index'])->name('feed.google');
Route::get('/feeds/google-shopping.xml', [FeedController::class, 'index'])->name('feed.google.shopping');
Route::get('/feeds/google-merchant-ch.xml/download', [FeedController::class, 'download'])->name('feed.google.download');
Route::get('/feeds/google-merchant-ch.tsv', [FeedController::class, 'tsv'])->name('feed.google.tsv');

Route::redirect('/feed/google-merchant.xml', '/feeds/google-merchant-ch.xml', 301);
Route::redirect('/feed/google-merchant.xml/download', '/feeds/google-merchant-ch.xml/download', 301);
