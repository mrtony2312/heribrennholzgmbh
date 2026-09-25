<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        $name = 'Heri Scheitholz Buche 1 Ster';

        return [
            'name' => $name,
            'slug' => Str::slug($name).'-'.fake()->unique()->numerify('###'),
            'sku' => 'HB-'.fake()->unique()->numerify('####'),
            'type' => 'simple',
            'short_description' => 'Trockenes Buchen-Scheitholz für Kaminöfen, 25 cm, aus Schweizer Produktion.',
            'description' => str_repeat('Trockenes Buchenholz mit geprüfter Restfeuchte für den privaten Heizbedarf. ', 8),
            'price' => 89.90,
            'regular_price' => 89.90,
            'sale_price' => null,
            'on_sale' => false,
            'currency' => 'CHF',
            'in_stock' => true,
            'weight' => '500',
        ];
    }
}
