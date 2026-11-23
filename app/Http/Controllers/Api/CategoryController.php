<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Http\Resources\CategoryTreeResource;
use Illuminate\Support\Facades\Cache;
use App\Services\CategoryTreeService;

class CategoryController extends Controller
{
    public function tree(CategoryTreeService $categoryTreeService)
    {
        return CategoryTreeResource::collection(
            collect($categoryTreeService->getTree())
        );
    }
}
