<?php

namespace App\Http\Controllers;

use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware(['isAdmin'], ['only' => ['changeStatus']]);
    }

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

    public function store(Request $request)
    {
        $request->validate([
            'items' => 'required|array'
        ]);

        $user = auth()->user();
        $orderNumber = 'ORD-' . time() . rand(100, 999);

        DB::beginTransaction();

        try {
            foreach ($request->items as $item) {

                $product = Product::findOrFail($item['product_id']);

                if ($product->qty < $item['count']) {
                    return response()->json([
                        'status' => false,
                        'message' => 'موجودی کافی نیست'
                    ], 400);
                }

                $total = $product->price * $item['count'];

                Order::create([
                    'order_number' => $orderNumber,
                    'user_id' => $user->id,
                    'product_id' => $product->id,
                    'count' => $item['count'],
                    'total' => $total,
                    'discount' => 0,
                    'status' => 'pending'
                ]);

                $product->decrement('qty', $item['count']);
            }

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'سفارش با موفقیت ثبت شد',
                'order_number' => $orderNumber
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => 'خطا در ثبت سفارش'
            ], 500);
        }
    }

    public function myOrders()
    {
        $orders = Order::where('user_id', auth()->id())
            ->with('product')
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy('order_number');

        return response()->json([
            'status' => true,
            'orders' => $orders
        ]);
    }

    public function show($orderNumber)
    {
        $orders = Order::where('order_number', $orderNumber)
            ->where('user_id', auth()->id())
            ->with('product')
            ->get();

        if ($orders->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'سفارش یافت نشد'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'order_number' => $orderNumber,
            'items' => $orders,
            'total_price' => $orders->sum('total')
        ]);
    }
}
