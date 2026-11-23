<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Parent category: Electronics
        $electronics = Category::updateOrCreate(
            ['slug' => 'electronics'],
            [
                'name' => 'Electronics',
                'parent_id' => null,
                'is_active' => true,
            ]
        );

        // Child categories of Electronics
        $computers = Category::updateOrCreate(
            ['slug' => 'computers'],
            [
                'name' => 'Computers',
                'parent_id' => $electronics->id,
                'is_active' => true,
            ]
        );

        $mobiles = Category::updateOrCreate(
            ['slug' => 'mobiles'],
            [
                'name' => 'Mobiles',
                'parent_id' => $electronics->id,
                'is_active' => false,
            ]
        );

        // Child categories of Computers
        Category::updateOrCreate(
            ['slug' => 'laptops'],
            [
                'name' => 'Laptops',
                'parent_id' => $computers->id,
                'is_active' => true,
            ]
        );

        Category::updateOrCreate(
            ['slug' => 'desktops'],
            [
                'name' => 'Desktops',
                'parent_id' => $computers->id,
                'is_active' => true,
            ]
        );

        // Child categories of inactive Mobiles
        Category::updateOrCreate(
            ['slug' => 'android'],
            [
                'name' => 'Android',
                'parent_id' => $mobiles->id,
                'is_active' => true,
            ]
        );

        Category::updateOrCreate(
            ['slug' => 'iphone'],
            [
                'name' => 'iPhone',
                'parent_id' => $mobiles->id,
                'is_active' => true,
            ]
        );

        // Another parent category
        $furniture = Category::updateOrCreate(
            ['slug' => 'furniture'],
            [
                'name' => 'Furniture',
                'parent_id' => null,
                'is_active' => true,
            ]
        );

        Category::updateOrCreate(
            ['slug' => 'office-furniture'],
            [
                'name' => 'Office Furniture',
                'parent_id' => $furniture->id,
                'is_active' => true,
            ]
        );

        Category::updateOrCreate(
            ['slug' => 'home-furniture'],
            [
                'name' => 'Home Furniture',
                'parent_id' => $furniture->id,
                'is_active' => true,
            ]
        );

        // Another root category
        Category::updateOrCreate(
            ['slug' => 'accessories'],
            [
                'name' => 'Accessories',
                'parent_id' => null,
                'is_active' => true,
            ]
        );
    }
}
