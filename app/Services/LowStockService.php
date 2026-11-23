<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use App\Models\Stock;

class LowStockService
{
    public function getLowStock(int $threshold = 10)
    {
        $cache = Cache::store('redis');

        // Version-based cache key.
        $cache->add('inventory:low-stock', 1);

        $version = $cache->get('inventory:low-stock', 1);

        $page = request()->integer('page', 1);
        $perPage = 10;

        $cacheKey = "inventory:low-stock:v1:version:{$version}:threshold:{$threshold}:page:{$page}:per_page:{$perPage}";

        $lowStock = $cache->get($cacheKey);

        if ($lowStock !== null) {
            return $lowStock;
        }

        $lowStock = Stock::query()
        ->join('products', 'stock.product_id', '=', 'products.id')
        ->join('warehouses', 'stock.warehouse_id', '=', 'warehouses.id')
        ->select(
            'products.id as product_id',
            'products.name as product_name',
            'products.sku',
            'warehouses.id as warehouse_id',
            'warehouses.name as warehouse_name',
            'stock.quantity',
            'stock.reserved_quantity',
            DB::raw('(stock.quantity - stock.reserved_quantity) as available_quantity')
        )
        ->where('products.is_active', true)
        ->lowStock($threshold)
        ->orderBy('available_quantity')
        ->paginate($perPage)
        ->withQueryString();

        // Do not cache empty results.
        if ($lowStock->isNotEmpty()) {
            $cache->put($cacheKey, $lowStock, now()->addHour());
        }

        return $lowStock;
    }
}
