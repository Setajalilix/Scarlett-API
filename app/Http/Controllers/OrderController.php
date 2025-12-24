<?php

namespace App\Http\Controllers;

use App\Http\Middleware\IsAdminMiddleware;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public static function middleware(): array
    {
        return [
            new Middleware(IsAdminMiddleware::class, only: ['changeStatus', 'index']),
        ];
    }

    public function index()
    {
        $orders = Order::with(['product', 'user'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy('order_number');
        return OrderResource::collection($orders);
    }

    public function changeStatus(Request $request, Order $order)
    {
        // if status == completed can not change it again
        if ($order->status == 'completed' or $order->status == 'cancelled') {
            return response()->json([
                'message' => 'This order is already completed or cancelled',
            ], 403);
        }
        $request->validate([
            'status' => 'required|in:pending,processing,completed',
        ]);
        if ($request->status == 'completed')
            $delivery_at = now();

        $order->update([
            'status' => $request->status,
            'delivery_at' => $delivery_at ?? null
        ]);
        return response()->json([
            'message' => 'Order status updated'
        ]);
    }

    public function cancelOrder(Order $order)
    {
        if ($order->status == 'completed' or $order->status == 'cancelled') {
            return response()->json([
                'message' => 'This order is already completed or cancelled',
            ], 403);
        }
        $order->update([
            'status' => 'cancelled'
        ]);
        return response()->json([
            'message' => 'Order cancelled'
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.product_id' => 'required|integer',
            'items.*.count' => 'required|integer',
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
            'orders' => OrderResource::collection($orders)
        ]);
    }

    public function show(Order $order)
    {

        $orders = Order::where('order_number', $order->order_number)
            ->where('user_id', auth()->id())
            ->with('product')
            ->get();

        if (!$orders) {
            return response()->json([
                'status' => false,
                'message' => 'سفارش یافت نشد'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'items' => new OrderResource($orders),
        ]);
    }
}
