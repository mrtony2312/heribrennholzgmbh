<?php

namespace Tests\Unit\Merchant;

use App\Domain\Merchant\Support\UnitPricingResolver;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UnitPricingResolverTest extends TestCase
{
    use RefreshDatabase;

    public function test_resolves_palette_bag_total_weight(): void
    {
        $product = Product::factory()->create([
            'name' => 'Pellet Ardenforest – 70 Säcke à 15 kg',
            'weight' => '15',
            'price' => 493.90,
        ]);
        $product->setRelation('categories', collect());

        $unit = UnitPricingResolver::resolve($product);

        $this->assertSame('1050 kg', $unit['measure']);
        $this->assertSame('1 kg', $unit['base']);
        $this->assertSame('1050 kg', $unit['shipping_weight']);
    }

    public function test_derives_kg_from_grundpreis(): void
    {
        $product = Product::factory()->create([
            'name' => 'Holzpellets Naturkraft – Palette mit 66 Säcke',
            'weight' => null,
            'price' => 470.00,
            'price_per_unit' => 0.470,
            'price_per_unit_label' => 'CHF/kg',
        ]);
        $product->setRelation('categories', collect());

        $unit = UnitPricingResolver::resolve($product);

        $this->assertSame('1000 kg', $unit['measure']);
        $this->assertSame('1 kg', $unit['base']);
    }
}
