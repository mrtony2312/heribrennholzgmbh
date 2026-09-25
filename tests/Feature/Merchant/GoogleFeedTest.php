<?php

namespace Tests\Feature\Merchant;

use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GoogleFeedTest extends TestCase
{
    use RefreshDatabase;

    public function test_xml_feed_contains_required_google_attributes(): void
    {
        $product = Product::factory()->create([
            'name' => 'Heri Buche Scheitholz',
            'price' => 49.50,
            'sku' => 'HB-TEST-1',
        ]);
        ProductImage::query()->create([
            'product_id' => $product->id,
            'path' => 'assets/images/demo/01.jpg',
            'is_primary' => true,
            'position' => 0,
        ]);

        $response = $this->get(route('feed.google'));
        $response->assertOk();
        $xml = $response->getContent();

        $this->assertStringContainsString('<g:id>'.$product->id.'</g:id>', $xml);
        $this->assertStringContainsString('<g:price>49.50 CHF</g:price>', $xml);
        $this->assertStringContainsString('<title>', $xml);
        $this->assertStringContainsString('g:country>CH</g:country>', $xml);
        $this->assertStringContainsString('1–2 Werktage', $xml);
        $this->assertStringNotContainsString('Liechtenstein', $xml);
        $this->assertStringNotContainsString('Deutschland', $xml);
    }

    public function test_pdp_json_ld_matches_feed_price(): void
    {
        $product = Product::factory()->create([
            'name' => 'Heri Buche Scheitholz',
            'price' => 49.50,
            'sku' => 'HB-TEST-2',
        ]);
        ProductImage::query()->create([
            'product_id' => $product->id,
            'path' => 'assets/images/demo/01.jpg',
            'is_primary' => true,
            'position' => 0,
        ]);

        $pdp = $this->get(route('product.show', $product));
        $pdp->assertOk();
        $pdp->assertSee('data-gmc-price="49.50"', false);
        $pdp->assertSee('1 bis 2 Werktage', false);
        $pdp->assertDontSee('wird bei der Bestellung berechnet', false);

        $this->assertStringContainsString('"price":"49.50"', $pdp->getContent());
        $this->assertStringContainsString('"priceCurrency":"CHF"', $pdp->getContent());

        $feed = $this->get(route('feed.google'))->getContent();
        $this->assertStringContainsString('<g:price>49.50 CHF</g:price>', $feed);
    }

    public function test_legal_pages_are_public(): void
    {
        foreach (['/kontakt', '/ueber-uns', '/versand', '/rueckgabe', '/datenschutz', '/agb'] as $path) {
            $this->get($path)->assertOk();
        }
        $this->get('/versand')->assertSee('1 bis 2 Werktage', false);
        $this->get('/kasse')->assertRedirect();
    }
}
