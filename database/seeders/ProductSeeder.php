<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\category;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categoryIds = Category::where('is_active', true)
            ->pluck('id')
            ->toArray();

        if (empty($categoryIds)) {
            throw new \RuntimeException('No active categories found. Seed categories first.');
        }

        for ($i = 1; $i <= 50; $i++) {
            Product::updateOrCreate(
                ['sku' => 'SKU-' . str_pad($i, 4, '0', STR_PAD_LEFT)],
                [
                    'category_id' => $categoryIds[($i - 1) % count($categoryIds)],
                    'name' => 'Product ' . $i,
                    'description' => 'Description for product ' . $i,
                    'base_price' => rand(500, 50000),
                    'is_active' => true,
                ]
            );
        }
    }
}
