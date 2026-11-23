<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductIndexRequest;
use App\Http\Resources\ProductResource;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use App\Services\ProductListingService;

class ProductController extends Controller
{

    public function index(ProductIndexRequest $request, ProductListingService $productListingService)
    {
        $products = $productListingService->getProducts(
            $request->validated()
        );

        return ProductResource::collection($products);
    }

}
