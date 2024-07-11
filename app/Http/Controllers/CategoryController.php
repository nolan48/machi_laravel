<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CategoryController extends Controller
{
    // 获取所有分类数据
    public function index()
    {
        try {
            $categories = Category::all();
            Log::info('Fetched categories', ['categories' => $categories]);
            return response()->json($categories);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch categories',], 500);
            Log::error('Failed to fetch categories', ['error' => $e->getMessage()]);
        }
    }
}
