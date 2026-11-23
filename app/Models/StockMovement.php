<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    protected $fillable = [
        'product_id',
        'warehouse_id',
        'movement_type',
        'quantity',
        'reference_id',
        'reference_type',
        'note',
        'moved_at',
    ];

    public $timestamps = false;

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function reference()
    {
        return $this->morphTo();
    }

    public function scopeOfType($query, $type)
    {
        return $query->where('movement_type', $type);
    }

    public function scopeBetweenDates($query, $from, $to)
    {
        return $query
            ->when($from, fn ($q) => $q->where(
                'moved_at',
                '>=',
                \Carbon\Carbon::parse($from)->startOfDay()
            ))
            ->when($to, fn ($q) => $q->where(
                'moved_at',
                '<',
                \Carbon\Carbon::parse($to)->addDay()->startOfDay()
            ));
    }
}
