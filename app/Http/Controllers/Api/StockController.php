<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdjustStockRequest;
use App\Models\Stock;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Cache;
use App\Services\StockAdjustmentService;
use App\Http\Resources\StockAdjustmentResource;

class StockController extends Controller
{


    public function adjust(AdjustStockRequest $request, StockAdjustmentService $stockAdjustmentService)
    {
        $result = $stockAdjustmentService->adjust(
            $request->validated()
        );

        // Cache invalidation happens only after the transaction succeeds.
        $cache = Cache::store('redis');

        $cache->forget('inventory:summary');
        $cache->increment('inventory:low-stock');

        return (new StockAdjustmentResource($result))
        ->additional([
            'message' => 'Stock adjusted successfully.',
        ]);
    }



}
