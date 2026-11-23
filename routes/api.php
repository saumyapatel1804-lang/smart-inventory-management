<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\StockController;
use App\Http\Controllers\Api\InventoryController;
use App\Http\Controllers\Api\ProductMovementController;



Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::get('/categories/tree', [CategoryController::class, 'tree']);

// Products
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{product}/movements',[ProductMovementController::class, 'index']);

// Stocks
Route::post('/stock/adjust', [StockController::class, 'adjust']);

// Inventory
Route::get('/inventory/summary', [InventoryController::class, 'summary']);
Route::get('/inventory/low-stock', [InventoryController::class, 'lowStock']);
