<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Warehouse;

class WarehouseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $warehouses = [
            [
                'name' => 'Ahmedabad Warehouse',
                'city' => 'Ahmedabad',
                'is_active' => true,
            ],
            [
                'name' => 'Surat Warehouse',
                'city' => 'Surat',
                'is_active' => true,
            ],
            [
                'name' => 'Vadodara Warehouse',
                'city' => 'Vadodara',
                'is_active' => true,
            ],
        ];

        foreach ($warehouses as $warehouse) {
            Warehouse::updateOrCreate(
                ['name' => $warehouse['name']],
                $warehouse
            );
        }
    }
}
