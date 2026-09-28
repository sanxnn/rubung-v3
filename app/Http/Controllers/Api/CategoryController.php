<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::with([
            'subcategories:id,category_id,name,slug',
        ])
            ->withCount('products')
            ->orderBy('name')
            ->get([
                'id',
                'name',
                'slug',
                'description',
            ]);

        return response()->json([
            'message' => 'Data kategori berhasil diambil.',
            'data' => $categories,
        ]);
    }

    public function show(Category $category)
    {
        $category->load([
            'subcategories:id,category_id,name,slug',
        ]);

        return response()->json([
            'message' => 'Detail kategori berhasil diambil.',
            'data' => $category,
        ]);
    }
}
