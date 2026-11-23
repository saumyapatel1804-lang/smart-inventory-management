<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InventorySummaryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'product_id' => (int) $this->product_id,
            'product_name' => $this->product_name,
            'total_quantity' => (int) $this->total_quantity,
            'total_reserved' => (int) $this->total_reserved,
            'available_quantity' => (int) $this->available_quantity,
            'highest_stock_warehouse' => $this->highest_stock_warehouse,
            'is_out_of_stock' => (int) $this->total_quantity === 0,
        ];
    }
}
