<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class ProductListingService
{
    public function getProducts(array $filters)
    {
        $warehouseId = $filters['warehouse_id'] ?? null;

        $query = Product::query()
            ->active()
            ->with([
                'category:id,name',
                'stocks' => function ($query) use ($warehouseId) {
                    if ($warehouseId !== null) {
                        $query->where('warehouse_id', $warehouseId);
                    }

                    $query->select(
                        'id',
                        'product_id',
                        'warehouse_id',
                        'quantity',
                        'reserved_quantity'
                    )->with('warehouse:id,name,city');
                },
            ]);

        // Product ID filter
        if (isset($filters['product_id'])) {
            $query->where('id', $filters['product_id']);
        }

        // Category filter
        if (isset($filters['category_id'])) {
            $categories = Category::query()
                ->select('id', 'parent_id')
                ->get();

            $childrenMap = $categories->groupBy('parent_id');

            $categoryIds = [$filters['category_id']];
            $pendingIds = [$filters['category_id']];

            while (!empty($pendingIds)) {
                $parentId = array_shift($pendingIds);

                foreach ($childrenMap->get($parentId, collect()) as $child) {
                    $categoryIds[] = $child->id;
                    $pendingIds[] = $child->id;
                }
            }

            $query->whereIn('category_id', $categoryIds);
        }

        // Warehouse filter
        if ($warehouseId !== null) {
            $query->whereHas('stocks', function ($stockQuery) use ($warehouseId) {
                $stockQuery->where('warehouse_id', $warehouseId);
            });
        }

        // Availability filter
        if (array_key_exists('available', $filters)) {
            $availabilityQuery = function ($stockQuery) use ($warehouseId) {
                if ($warehouseId !== null) {
                    $stockQuery->where('warehouse_id', $warehouseId);
                }

                $stockQuery->available();
            };

            if ($filters['available']) {
                $query->whereHas('stocks', $availabilityQuery);
            } else {
                $query->whereDoesntHave('stocks', $availabilityQuery);
            }
        }

        // Price filters
        $query->priceBetween(
            $filters['min_price'] ?? null,
            $filters['max_price'] ?? null
        );

        // Search by name or SKU
        $query->when($filters['search'] ?? null, function ($q, $search) {
            $q->search($search);
        });

        // Calculate available stock
        $query->addSelect([
            'available_stock' => DB::table('stock')
                ->selectRaw(
                    'COALESCE(SUM(quantity - reserved_quantity), 0)'
                )
                ->whereColumn('stock.product_id', 'products.id')
                ->when($warehouseId !== null, function ($stockQuery) use ($warehouseId) {
                    $stockQuery->where('warehouse_id', $warehouseId);
                }),
        ]);

        // Sorting
        $sortBy = $filters['sort_by'] ?? 'name';
        $sortOrder = $filters['sort_order'] ?? 'asc';

        $sortColumn = match ($sortBy) {
            'price' => 'base_price',
            'available_stock' => 'available_stock',
            default => 'name',
        };

        $query->orderBy($sortColumn, $sortOrder)
            ->orderBy('id', 'asc');

        // Pagination
        return $query->paginate($filters['per_page'] ?? 10)
            ->withQueryString();
    }
}
