<?php

namespace App\Http\Controllers;

use App\Http\Middleware\IsAdminMiddleware;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\Middleware;

class CategoryController extends Controller
{
    public static function middleware(): array
    {
        return [
            new Middleware(IsAdminMiddleware::class, except: ['index', 'show']),
        ];
    }

    public function index()
    {
        $categories = Category::all();
        return CategoryResource::collection($categories);
    }

    public function show(Category $category)
    {
        return new CategoryResource($category);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required',
        ]);
        Category::create($data);
        return response()->json([
            'message' => 'Category created successfully'
        ], 201);
    }

    public function update(Request $request, Category $category)
    {
        $data = $request->validate([
            'title' => 'required',
        ]);
        $category->update($data);
        return response()->json([
            'message' => 'Category updated successfully'
        ]);
    }

    public function destroy(Category $category)
    {
        $category->delete();
        return response()->json([
            'message' => 'Category deleted successfully'
        ]);
    }
}
