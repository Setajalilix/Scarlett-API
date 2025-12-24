<?php

use App\Http\Controllers\ProductController;
use App\Http\Middleware\IsAdminMiddleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\Auth\AuthController;
use \App\Http\Controllers\OrderController;
use \App\Http\Controllers\CategoryController;

Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);
Route::post('/tokens/create', function (Request $request) {
    $token = $request->user()->createToken($request->token_name);

    return ['token' => $token->plainTextToken];
});
//Route::middleware('auth:sanctum')->group(function () {
Route::apiResource('products', ProductController::class);
Route::apiResource('categories', CategoryController::class);
Route::post('orders', [OrderController::class, 'store']);
Route::get('my-orders', [OrderController::class, 'myOrders']);
Route::get('orders/{orderNumber}', [OrderController::class, 'show']);

Route::post('orders/{orderNumber}/status', [OrderController::class, 'changeStatus']);
//});

