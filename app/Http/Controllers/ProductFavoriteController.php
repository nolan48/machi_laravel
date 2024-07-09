<?php
namespace App\Http\Controllers;

use App\Models\ProductFavorite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ProductFavoriteController extends Controller
{
    // 獲得某會員id的有加入到我的最愛清單中的商品id們
    public function index(Request $request)
    {
        $user = $request->user(); // 获取当前用户
        $uid = $user->user_id; // 获取用户ID

        $favorites = ProductFavorite::where('user_id_fk', $uid)
            ->pluck('product_id_fk')
            ->toArray();

        return response()->json(['status' => 'success', 'data' => ['favorites' => $favorites]]);
    }

    // 新增我的最愛商品
    public function store($id, Request $request)
    {
        $user = $request->user(); // 获取当前用户
        Log::info($user);

        $uid = $user->user_id; // 获取用户ID

        $existFav = ProductFavorite::where([
            ['product_id_fk', '=', $id],
            ['user_id_fk', '=', $uid]
        ])->first();

        if ($existFav) {
            return response()->json(['status' => 'error', 'message' => '资料已经存在，新增失败']);
        }

        $newFav = ProductFavorite::create([
            'product_id_fk' => $id,
            'user_id_fk' => $uid,
        ]);

        if (!$newFav) {
            return response()->json(['status' => 'error', 'message' => '新增失败']);
        }

        return response()->json(['status' => 'success', 'data' => null]);
    }

    // 刪除我的最愛商品
    public function destroy($id, Request $request)
    {
        $user = $request->user(); // 获取当前用户
        $uid = $user->user_id; // 获取用户ID

        $affectedRows = ProductFavorite::where([
            ['product_id_fk', '=', $id],
            ['user_id_fk', '=', $uid]
        ])->delete();

        if (!$affectedRows) {
            return response()->json(['status' => 'error', 'message' => '刪除失敗']);
        }

        return response()->json(['status' => 'success', 'data' => null]);
    }
}
