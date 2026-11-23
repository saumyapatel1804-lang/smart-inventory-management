<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\ProductMovementRequest;
use App\Http\Resources\ProductMovementResource;
use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Services\ProductMovementService;


class ProductMovementController extends Controller
{

    public function index(ProductMovementRequest $request,Product $product,ProductMovementService $productMovementService)
    {
        $result = $productMovementService->getMovements(
            $product,
            $request->validated()
        );

        return ProductMovementResource::collection($result['movements'])
            ->additional([
                'summary' => $result['summary'],
            ]);
    }

}
