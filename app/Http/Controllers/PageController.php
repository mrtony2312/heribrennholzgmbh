<?php

namespace App\Http\Controllers;

use App\Mail\ContactFormMail;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class PageController extends Controller
{
    public function about()
    {
        return view('pages.about');
    }

    public function contact()
    {
        return view('pages.contact');
    }

    public function contactSubmit(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:150'],
            'subject' => ['nullable', 'string', 'max:150'],
            'message' => ['required', 'string', 'max:2000'],
        ]);

        $admin = (string) config('mail.admin.address', config('mail.from.address'));
        try {
            Mail::to($admin)->send(new ContactFormMail(
                name: $request->string('name')->toString(),
                email: $request->string('email')->toString(),
                contactSubject: $request->filled('subject') ? $request->string('subject')->toString() : null,
                body: $request->string('message')->toString(),
            ));
        } catch (\Throwable $e) {
            Log::error('Kontaktformular fehlgeschlagen', ['error' => $e->getMessage()]);
        }

        return back()->with('status', 'Vielen Dank, Ihre Nachricht wurde gesendet. Wir melden uns in Kürze.');
    }

    public function faq()
    {
        return view('pages.faq');
    }

    public function privacy()
    {
        return view('pages.privacy');
    }

    public function terms()
    {
        return view('pages.terms');
    }

    public function impressum()
    {
        return view('pages.impressum');
    }

    public function versand()
    {
        return view('pages.versand');
    }

    public function rueckgabe()
    {
        return view('pages.rueckgabe');
    }

    public function sitemap(): Response
    {
        $urls = [
            url('/'),
            route('shop.index'),
            route('pages.about'),
            route('pages.contact'),
            route('pages.versand'),
            route('pages.rueckgabe'),
            route('pages.privacy'),
            route('pages.terms'),
            route('pages.impressum'),
            route('pages.faq'),
        ];

        foreach (Category::query()->orderBy('position')->get() as $category) {
            $urls[] = route('shop.category', $category);
        }
        foreach (Product::query()->orderBy('id')->get() as $product) {
            $urls[] = route('product.show', $product);
        }

        $xml = '<?xml version="1.0" encoding="UTF-8"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
        foreach ($urls as $loc) {
            $xml .= '<url><loc>'.e($loc).'</loc></url>';
        }
        $xml .= '</urlset>';

        return response($xml, 200, ['Content-Type' => 'application/xml; charset=UTF-8']);
    }
}
