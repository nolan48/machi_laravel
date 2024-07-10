<?php



namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
class OrderController extends Controller
{
    // 獲取訂單項目
    public function getOrderItems(Request $request)
    {
        $orderId = $request->query('oid');

        try {
            $orderItems = OrderItem::where('order_id_fk', $orderId)->get();

            if ($orderItems->isNotEmpty()) {
                return response()->json($orderItems);
            } else {
                return response()->json(['status' => 'error', 'message' => 'No order items found for the given order ID'], 404);
            }
        } catch (\Exception $e) {
            Log::error('Error fetching order items:', ['error' => $e->getMessage()]);
            return response()->json(['status' => 'error', 'message' => 'Internal server error', 'error' => $e->getMessage()], 500);
        }
    }

    // 獲取訂單
    public function getOrders(Request $request)
    {
        $userId = $request->query('userId');
        $start = $request->query('start', '1970-01-01');
        $end = $request->query('end', '2050-01-01');
        $selectedStatus = $request->query('selectedStatus', '');

        $conditions = [];

        if ($userId) {
            $conditions[] = ['user_id_fk', '=', $userId];
        }

        if ($selectedStatus) {
            if ($selectedStatus === '已完成') {
                $conditions[] = ['order_status', '=', $selectedStatus];
            } else {
                $conditions[] = ['order_status', '!=', '已完成'];
            }
        }

        $startDate = Carbon::parse($start)->startOfDay();
        $endDate = Carbon::parse($end)->endOfDay();

        $conditions[] = ['order_createtime', '>=', $startDate];
        $conditions[] = ['order_createtime', '<=', $endDate];

        $page = intval($request->query('page', 1));
        $perpage = intval($request->query('perpage', 16));
        $offset = ($page - 1) * $perpage;
        $limit = $perpage;

        $sortField = 'order_createtime';
        $orderDirection = 'DESC';

        try {
            $ordersQuery = Order::where($conditions)
                ->orderBy($sortField, $orderDirection)
                ->offset($offset)
                ->limit($limit);

            $orders = $ordersQuery->get();
            $total = $ordersQuery->count();
            $pageCount = ceil($total / $perpage);

            if ($request->query('raw') === 'true') {
                return response()->json($orders);
            }

            return response()->json([
                'total' => $total,
                'pageCount' => $pageCount,
                'page' => $page,
                'perpage' => $perpage,
                'orders' => $orders,
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching orders:', ['error' => $e->getMessage()]);
            return response()->json(['status' => 'error', 'message' => 'Internal server error', 'error' => $e->getMessage()], 500);
        }
    }

    // 創建訂單
    public function createOrder(Request $request)
    {
        DB::beginTransaction();

        try {
            // 创建订单
            $orderData = $request->input('data.data');
            $order = Order::create([
                'user_id_fk' => $orderData['user_id_fk'],
                'order_payment' => $orderData['payment'],
                'order_username' => $orderData['username'],
                'order_address' => $orderData['address'],
                'order_phone' => $orderData['phone'],
                'order_amount' => $orderData['amount'],
                'order_total' => $orderData['total'],
                'order_status' => '已付款', // 添加 order_status 字段
            ]);

            // 获取订单项数据
            $orderItemsData = $request->input('data.items');

            // 为每个订单项添加order_id_fk字段
            $orderItems = array_map(function ($item) use ($order) {
                return [
                    'order_id_fk' => $order->order_id,
                    'order_product_type' => $item['product_type'],
                    'order_product_id' => $item['product_id'],
                    'order_product_name' => $item['product_name'],
                    'order_product_detail' => $item['product_detail'] ?? '', // 如果 product_detail 為空，設置為空字符串
                    'order_product_count' => $item['product_count'],
                    'order_product_price' => $item['product_price'],
                ];
            }, $orderItemsData);

            // 创建OrderItem
            OrderItem::insert($orderItems);

            DB::commit();

            return response()->json(['status' => 'success', 'data' => $order, 'items' => $orderItems]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating order and order items:', ['error' => $e->getMessage()]);
            return response()->json(['status' => 'error', 'message' => 'Internal server error', 'error' => $e->getMessage()], 500);
        }
    }
}
