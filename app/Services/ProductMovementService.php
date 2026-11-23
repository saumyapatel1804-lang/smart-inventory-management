<?php

namespace App\Services;

use App\Models\Product;
use App\Models\StockMovement;

class ProductMovementService
{
    public function getMovements(Product $product, array $filters): array
    {
        $query = StockMovement::query()
        ->where('product_id', $product->id)
        ->betweenDates(
            $filters['date_from'] ?? null,
            $filters['date_to'] ?? null
        );

        // Calculate totals directly in the database.
        $summary = (clone $query)
            ->selectRaw("
                COALESCE(SUM(
                    CASE WHEN movement_type = 'stock_in'
                    THEN quantity ELSE 0 END
                ), 0) as total_in,

                COALESCE(SUM(
                    CASE WHEN movement_type = 'stock_out'
                    THEN quantity ELSE 0 END
                ), 0) as total_out
            ")
            ->first();

        $totalIn = (int) $summary->total_in;
        $totalOut = (int) $summary->total_out;

        // Paginated movement history.
        $movements = $query
            ->orderByDesc('moved_at')
            ->orderByDesc('id')
            ->paginate($filters['per_page'] ?? 20)
            ->withQueryString();

        return [
            'movements' => $movements,
            'summary' => [
                'total_in' => $totalIn,
                'total_out' => $totalOut,
                'net_movement' => $totalIn - $totalOut,
            ],
        ];
    }
}
