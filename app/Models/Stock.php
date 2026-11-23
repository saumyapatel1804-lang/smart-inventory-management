<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
     protected $table = 'stock';

     protected $fillable = [
        'product_id',
        'warehouse_id',
        'quantity',
        'reserved_quantity',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'reserved_quantity' => 'integer',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function scopeAvailable($query)
    {
        return $query->whereColumn('quantity', '>', 'reserved_quantity');
    }

    public function scopeLowStock($query, $threshold = 10)
    {
        return $query->whereRaw(
            '(quantity - reserved_quantity) <= ?',
            [$threshold]
        );
    }

}
