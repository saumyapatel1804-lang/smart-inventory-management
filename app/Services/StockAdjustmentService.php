<?php

namespace App\Services;

use App\Models\Stock;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class StockAdjustmentService
{
    public function adjust(array $data): array
    {
        return DB::transaction(function () use ($data) {

            // Create the stock row if it does not exist.
            DB::table('stock')->insertOrIgnore([
                'product_id' => $data['product_id'],
                'warehouse_id' => $data['warehouse_id'],
                'quantity' => 0,
                'reserved_quantity' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Lock the row to prevent concurrent stock modifications.
            $stock = Stock::where('product_id', $data['product_id'])
                ->where('warehouse_id', $data['warehouse_id'])
                ->lockForUpdate()
                ->firstOrFail();

            $quantity = $data['quantity'];

            switch ($data['movement_type']) {

                case 'stock_in':
                    $stock->quantity += $quantity;
                    break;

                case 'stock_out':
                    $available = $stock->quantity - $stock->reserved_quantity;

                    if ($available < $quantity) {
                        throw ValidationException::withMessages([
                            'quantity' => ['Insufficient available stock.'],
                        ]);
                    }

                    $stock->quantity -= $quantity;
                    break;

                case 'reservation':
                    $available = $stock->quantity - $stock->reserved_quantity;

                    if ($available < $quantity) {
                        throw ValidationException::withMessages([
                            'quantity' => ['Not enough available stock to reserve.'],
                        ]);
                    }

                    $stock->reserved_quantity += $quantity;
                    break;

                case 'reservation_release':
                    if ($stock->reserved_quantity < $quantity) {
                        throw ValidationException::withMessages([
                            'quantity' => ['Cannot release more stock than reserved.'],
                        ]);
                    }

                    $stock->reserved_quantity -= $quantity;
                    break;
            }

            $stock->save();

            // Record every successful stock movement.
            $movement = StockMovement::create([
                'product_id' => $data['product_id'],
                'warehouse_id' => $data['warehouse_id'],
                'movement_type' => $data['movement_type'],
                'quantity' => $quantity,
                'reference_id' => null,
                'reference_type' => null,
                'note' => $data['note'] ?? null,
                'moved_at' => now(),
            ]);

            return [
                'stock' => $stock,
                'movement' => $movement,
            ];
        });
    }
}
