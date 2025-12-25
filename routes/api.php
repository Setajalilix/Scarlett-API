<?php

use App\Http\Controllers\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\Auth\AuthController;
use \App\Http\Controllers\OrderController;
use \App\Http\Controllers\CategoryController;
use \App\Http\Controllers\MessageController;

Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);
Route::post('/tokens/create', function (Request $request) {
    $token = $request->user()->createToken($request->token_name);

    return ['token' => $token->plainTextToken];
});
Route::middleware('auth:sanctum')->group(function () {
    Route::post('chat/send', [MessageController::class, 'send']);
    Route::get('chat/{userId}', [MessageController::class, 'history']);

    Route::apiResource('products', ProductController::class);
    Route::apiResource('categories', CategoryController::class);
    Route::get('categories/{category}/products', [CategoryController::class, 'showProducts']);

    Route::apiResource('orders', OrderController::class)->except(['update', 'destroy']);
    Route::get('my-orders', [OrderController::class, 'myOrders']);

    Route::post('orders/{order}/status', [OrderController::class, 'changeStatus']);
    Route::post('orders/{order}/cancel', [OrderController::class, 'cancelOrder']);

});

