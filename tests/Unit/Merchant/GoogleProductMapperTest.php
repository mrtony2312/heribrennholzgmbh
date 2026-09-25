<?php

namespace Tests\Unit\Merchant;

use App\Domain\Merchant\GoogleProductMapper;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GoogleProductMapperTest extends TestCase
{
    use RefreshDatabase;

    public function test_maps_eligible_product(): void
    {
        $product = Product::factory()->create(['price' => 29.99, 'sku' => 'SKU-1']);
        ProductImage::query()->create([
            'product_id' => $product->id,
            'path' => 'assets/images/demo/01.jpg',
            'is_primary' => true,
            'position' => 0,
        ]);
        $product->load('images', 'categories');

        $dto = app(GoogleProductMapper::class)->map($product);

        $this->assertNotNull($dto);
        $this->assertSame((string) $product->id, $dto->id);
        $this->assertSame('29.99 CHF', $dto->price);
        $this->assertSame('in_stock', $dto->availability);
        $this->assertSame('SKU-1', $dto->mpn);
        $this->assertNull($dto->gtin);
    }

    public function test_skips_zero_price(): void
    {
        $product = Product::factory()->create(['price' => 0]);
        ProductImage::query()->create([
            'product_id' => $product->id,
            'path' => 'assets/images/demo/01.jpg',
            'is_primary' => true,
        ]);
        $product->load('images');

        $this->assertNull(app(GoogleProductMapper::class)->map($product));
    }
}
