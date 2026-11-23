<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class InventorySummaryService
{
    public function getSummary()
    {
        $cache = Cache::store('redis');

        $cacheKey = 'inventory:summary';

        $summary = $cache->get($cacheKey);

        if ($summary !== null) {
            return $summary;
        }

        $summary = DB::table('products')
            ->Join('stock', 'products.id', '=', 'stock.product_id')
            ->select(
                'products.id as product_id',
                'products.name as product_name',
                DB::raw('COALESCE(SUM(stock.quantity), 0) as total_quantity'),
                DB::raw('COALESCE(SUM(stock.reserved_quantity), 0) as total_reserved'),
                DB::raw('COALESCE(SUM(stock.quantity - stock.reserved_quantity), 0) as available_quantity')
            )
            ->selectRaw("
                (
                    SELECT w.name
                    FROM stock s
                    INNER JOIN warehouses w ON w.id = s.warehouse_id
                    WHERE s.product_id = products.id
                    ORDER BY s.quantity DESC, w.id ASC
                    LIMIT 1
                ) as highest_stock_warehouse
            ")
            ->groupBy('products.id', 'products.name')
            ->get();

        if ($summary->isNotEmpty()) {
            $cache->put($cacheKey, $summary, now()->addHour());
        }

        return $summary;
    }
}
