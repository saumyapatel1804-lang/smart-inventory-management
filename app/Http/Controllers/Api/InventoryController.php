<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\InventorySummaryResource;
use App\Http\Resources\LowStockResource;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;
use App\Http\Requests\LowStockRequest;
use App\Services\InventorySummaryService;
use App\Services\LowStockService;

class InventoryController extends Controller
{
    public function summary(InventorySummaryService $inventorySummaryService)
    {
        $summary = $inventorySummaryService->getSummary();

        return InventorySummaryResource::collection($summary);
    }

    public function lowStock(LowStockRequest $request,LowStockService $lowStockService)
    {
        $threshold = $request->validated()['threshold'] ?? 10;

        $lowStock = $lowStockService->getLowStock($threshold);

        return LowStockResource::collection($lowStock)
            ->additional(['threshold' => $threshold]);
    }
}
