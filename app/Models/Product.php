<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{

    protected $fillable = [
        'category_id',
        'name',
        'sku',
        'description',
        'base_price',
        'is_active',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function stocks()
    {
        return $this->hasMany(Stock::class);
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'LIKE', "%{$search}%")
            ->orWhere('sku', 'LIKE', "%{$search}%");
        });
    }

    public function scopePriceBetween($query, $min, $max)
    {
        return $query
            ->when($min !== null, fn ($q) => $q->where('base_price', '>=', $min))
            ->when($max !== null, fn ($q) => $q->where('base_price', '<=', $max));
    }

}
