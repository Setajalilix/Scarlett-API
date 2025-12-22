<?php

namespace App\Http\Controllers;

use App\Http\Resources\OrderResource;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::all();
        return OrderResource::collection($orders);
    }

    public function changeStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required'
        ]);
        $order->update([
            'status' => $request->status
        ]);
        return response()->json([
            'message' => 'Order status updated'
        ]);
    }
}
