<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StockAdjustmentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $stock = $this->resource['stock'];
        $movement = $this->resource['movement'];

        return [
            'product_id' => $stock->product_id,
            'warehouse_id' => $stock->warehouse_id,
            'quantity' => $stock->quantity,
            'reserved_quantity' => $stock->reserved_quantity,
            'available_quantity' => $stock->quantity - $stock->reserved_quantity,

            'movement' => [
                'id' => $movement->id,
                'movement_type' => $movement->movement_type,
                'quantity' => $movement->quantity,
                'note' => $movement->note,
                'moved_at' => $movement->moved_at,
            ],
        ];
    }
}
