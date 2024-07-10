<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CartItem;
use Illuminate\Support\Facades\Log;
class CartItemController extends Controller
{
    // 獲得某會員id的有加入到購物清單中的商品id們
    public function index(Request $request)
    {
        try {
            $userId = $request->query('user_id');
            $cartItems = CartItem::where('user_id_fk', $userId)->get();
            return response()->json(['status' => 'success', 'items' => $cartItems]);
        } catch (\Exception $e) {
            Log::error('Error fetching cart items:', ['error' => $e->getMessage()]);
            return response()->json(['status' => 'error', 'message' => 'Internal server error'], 500);
        }
    }

    // 更新購物車
    public function update(Request $request)
    {
        try {
            $userId = intval($request->input('uid'));
            $itemId = intval($request->input('id'));
            $newQuantity = intval($request->input('quantity'));
            $newType = strval($request->input('type'));

            if (!$newQuantity || $newQuantity < 1) {
                return response()->json(['status' => 'error', 'message' => 'Invalid quantity provided'], 400);
            }

            switch ($newType) {
                case 'product':
                    $fieldCount = 'product_count';
                    $fieldType = 'product_id_fk';
                    break;
                case 'course':
                    $fieldCount = 'course_count';
                    $fieldType = 'course_id_fk';
                    break;
                case 'custom':
                    $fieldCount = 'custom_count';
                    $fieldType = 'cart_item_id';
                    break;
                default:
                    return response()->json(['status' => 'error', 'message' => 'Invalid type provided'], 400);
            }

            $updatedItem = CartItem::where($fieldType, $itemId)
                ->where('user_id_fk', $userId)
                ->update([$fieldCount => $newQuantity]);

            if ($updatedItem > 0) {
                return response()->json(['status' => 'success', 'message' => "$fieldType updated successfully"]);
            } else {
                return response()->json(['status' => 'error', 'message' => 'Cart item not found'], 404);
            }
        } catch (\Exception $e) {
            Log::error('Error updating cart item:', ['error' => $e->getMessage()]);
            return response()->json(['status' => 'error', 'message' => 'Internal server error'], 500);
        }
    }

    // 刪除購物車一筆資料
    public function destroy(Request $request)
    {
        try {
            $itemId = intval($request->query('id'));
            $newType = strval($request->query('type'));
            $userId = intval($request->query('uid'));

            if (is_nan($itemId) || $itemId <= 0) {
                return response()->json(['status' => 'error', 'message' => 'Invalid item ID provided'], 400);
            }

            switch ($newType) {
                case 'product':
                    $fieldName = 'product_id_fk';
                    break;
                case 'course':
                    $fieldName = 'course_id_fk';
                    break;
                case 'custom':
                    $fieldName = 'cart_item_id';
                    break;
                default:
                    return response()->json(['status' => 'error', 'message' => 'Invalid type provided'], 400);
            }

            $result = CartItem::where($fieldName, $itemId)
                ->where('user_id_fk', $userId)
                ->delete();

            if ($result > 0) {
                return response()->json(['status' => 'success', 'message' => 'Cart item deleted successfully']);
            } else {
                return response()->json(['status' => 'error', 'message' => 'Cart item not found'], 404);
            }
        } catch (\Exception $e) {
            Log::error('Error deleting cart item:', ['error' => $e->getMessage()]);
            return response()->json(['status' => 'error', 'message' => 'Internal server error'], 500);
        }
    }

    // 加入購物車
    public function store(Request $request)
    {
        try {
            $userId = intval($request->query('uid'));
            $cartItem = $request->input('data');
            $newType = strval($cartItem['type']);

            switch ($newType) {
                case 'product':
                    $fieldId = 'product_id_fk';
                    $fieldName = 'product_name';
                    $fieldPrice = 'product_price';
                    $fieldQuantity = 'product_count';
                    break;
                case 'course':
                    $fieldId = 'course_id_fk';
                    $fieldName = 'course_name';
                    $fieldPrice = 'course_price';
                    $fieldQuantity = 'course_count';
                    break;
                case 'custom':
                    $fieldPrice = 'custom_price';
                    $fieldQuantity = 'custom_count';
                    break;
                default:
                    return response()->json(['status' => 'error', 'message' => 'type傳輸有誤'], 400);
            }

            $addItemData = [
                $fieldPrice => $cartItem['price'],
                $fieldQuantity => $cartItem['quantity'],
                'user_id_fk' => $userId,
            ];

            if ($newType !== 'custom') {
                $addItemData[$fieldId] = $cartItem['id'];
                $addItemData[$fieldName] = $cartItem['name'];
            }

            if ($newType === 'course') {
                $addItemData['course_address'] = $cartItem['course_address'];
                $addItemData['course_date'] = $cartItem['course_date'];
            }

            if ($newType === 'product') {
                $addItemData['product_subtitle'] = $cartItem['subtitle'];
            }

            if ($newType === 'custom') {
                $addItemData['custom_size'] = $cartItem['size'];
                $addItemData['custom_layer'] = $cartItem['layer'];
                $addItemData['custom_decor'] = $cartItem['decor'];
                $addItemData['custom_flavor'] = $cartItem['flavor'];
                $addItemData['custom_img'] = $cartItem['custom_img'];
            }

            $addedItem = CartItem::create($addItemData);

            if ($addedItem) {
                return response()->json(['status' => 'success', 'message' => "UserId:$userId, Cart item added successfully"]);
            } else {
                return response()->json(['status' => 'error', 'message' => 'Cart item not found'], 404);
            }
        } catch (\Exception $e) {
            Log::error('Error updating cart item:', ['error' => $e->getMessage()]);
            return response()->json(['status' => 'error', 'message' => 'Internal server error'], 500);
        }
    }
}
