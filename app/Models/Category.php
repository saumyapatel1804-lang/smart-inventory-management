<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Category extends Model
{
    protected $fillable = [
        'name',
        'parent_id',
        'slug',
        'is_active',
    ];

    // Get the parent category
    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    protected static function booted(): void
    {
        static::saved(function ($category) {
            Cache::store('redis')->forget('categories:tree');
        });

        static::deleted(function ($category) {
            Cache::store('redis')->forget('categories:tree');
        });
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
