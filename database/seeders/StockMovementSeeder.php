<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Warehouse;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;

class StockMovementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $productIds = Product::pluck('id')->toArray();
        $warehouseIds = Warehouse::pluck('id')->toArray();

        if (empty($productIds) || empty($warehouseIds)) {
            throw new \RuntimeException(
                'Products and warehouses data is empty.'
            );
        }

        $movementTypes = [
            'stock_in',
            'stock_out',
            'reservation',
            'reservation_release',
        ];

        // Remove previous sample movements before creating 200 new ones.
        DB::table('stock_movements')->delete();

        for ($i = 1; $i <= 200; $i++) {
            StockMovement::create([
                'product_id' => fake()->randomElement($productIds),
                'warehouse_id' => fake()->randomElement($warehouseIds),
                'movement_type' => fake()->randomElement($movementTypes),
                'quantity' => fake()->numberBetween(1, 20),
                'reference_id' => null,
                'reference_type' => null,
                'note' => 'Sample stock movement ' . $i,
                'moved_at' => fake()->dateTimeBetween('-90 days', 'now'),
            ]);
        }
    }
}
