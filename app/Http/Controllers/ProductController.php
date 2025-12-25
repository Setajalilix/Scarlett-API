<?php

namespace App\Http\Controllers;

use App\Http\Middleware\IsAdminMiddleware;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Services\ImageService;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;

class ProductController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware(IsAdminMiddleware::class, except: ['index', 'show']),
        ];
    }

    public function index()
    {
        $products = Product::all();

        return ProductResource::collection($products);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'qty' => 'nullable|numeric|min:0',
            'category_id' => 'required|integer|exists:categories,id',

        ]);
        $imgService = new ImageService();
        $data['image'] = $imgService->upload($request->file('image'), $request->name);
        Product::create($data);
        return response()->json([
            'message' => 'Product created successfully'
        ], 201);
    }

    public function show(Product $product)
    {
        return new ProductResource($product);
    }

    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'qty' => 'nullable|numeric|min:0',
        ]);
        if ($request->hasFile('image')) {
            $imgService = new ImageService();
            $imgService->delete($product->image);
            $data['image'] = $imgService->upload($request->file('image'), $request->name);
        }
        $product->update($data);
        return response()->json([
            'message' => 'Product updated successfully'
        ]);
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return response()->json([
            'message' => 'Product deleted successfully'
        ]);
    }
}
