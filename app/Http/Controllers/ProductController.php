<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $sort = $request->query('sort', 'date');
        $order = $request->query('order', 'desc');
        $search = $request->query('search', '');
        $category = $request->query('category', '');
        $min = $request->query('min', 0);
        $max = $request->query('max', 3000);

        $query = Product::query();

        if ($search) {
            $query->where('product_name', 'like', "%$search%");
        }

        if ($category) {
            $query->where('product_category', $category);
        }

        if ($min || $max) {
            $query->whereBetween('product_price_small', [$min, $max]);
        }

        $page = $request->query('page', 1);
        $perpage = $request->query('perpage', 16);
        $offset = ($page - 1) * $perpage;

        $sortMap = [
            'date' => 'product_createtime',
            'price' => 'product_price_small',
        ];
        $sortField = $sortMap[$sort] ?? 'product_createtime';
        $orderDirection = $order ?? 'ASC';

        $products = $query->orderBy($sortField, $orderDirection)
                          ->offset($offset)
                          ->limit($perpage)
                          ->get();

        $count = $query->count();
        $pageCount = ceil($count / $perpage) ?: 0;

        if ($request->query('raw') === 'true') {
            return response()->json($products);
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'total' => $count,
                'pageCount' => $pageCount,
                'page' => $page,
                'perpage' => $perpage,
                'products' => $products,
            ],
        ]);
    }

    public function show($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        return response()->json($product);
    }
}
