<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Stock;
use App\Models\Warehouse;

class StockSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = Product::orderBy('id')->get();
        $warehouses = Warehouse::orderBy('id')->get();

        if ($products->isEmpty() || $warehouses->isEmpty()) {
            throw new \RuntimeException('Products or warehouses data is empty.');
        }

        foreach ($products as $product) {
            foreach ($warehouses as $warehouse) {

                // Keep product 1 at zero stock in every warehouse
                if ($product->id === $products->first()->id) {
                    $quantity = 0;
                    $reserved = 0;
                } else {
                    $quantity = rand(0, 100);

                    // Reserved quantity cannot exceed physical quantity
                    $reserved = $quantity > 0
                        ? rand(0, $quantity)
                        : 0;
                }

                Stock::updateOrCreate(
                    [
                        'product_id' => $product->id,
                        'warehouse_id' => $warehouse->id,
                    ],
                    [
                        'quantity' => $quantity,
                        'reserved_quantity' => $reserved,
                    ]
                );
            }
        }
    }
}
