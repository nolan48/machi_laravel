<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\LinePayService;
use App\Models\Order;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    protected $linePayService;

    public function __construct(LinePayService $linePayService)
    {
        $this->linePayService = $linePayService;
    }

    public function reserve(Request $request)
    {
        Log::info('PaymentController reserve method called', ['request' => $request->all()]);

        if (!$request->query('orderId')) {
            Log::error('order id不存在');
            return response()->json(['status' => 'error', 'message' => 'order id不存在']);
        }

        $orderId = $request->query('orderId');

        // 确认环境变量存在
        $confirmUrl = env('REACT_REDIRECT_CONFIRM_URL');
        $cancelUrl = env('REACT_REDIRECT_CANCEL_URL');
        if (!$confirmUrl || !$cancelUrl) {
            Log::error('Redirect URLs are not set in the environment variables.');
            return response()->json(['status' => 'error', 'message' => '配置错误'], 500);
        }

        // 設定重新導向與失敗導向的網址
        $redirectUrls = [
            'confirmUrl' => $confirmUrl,
            'cancelUrl' => $cancelUrl,
        ];

        // 从数据库取得订单数据
        $orderRecord = Order::find($orderId);

        // 确保订单记录存在
        if (!$orderRecord) {
            Log::error('訂單不存在', ['orderId' => $orderId]);
            return response()->json(['status' => 'error', 'message' => '訂單不存在']);
        }

        $order = [
            'orderId' => $orderId,
            'currency' => 'TWD',
            'amount' => $orderRecord->order_total,
            'packages' => [
                [
                    'id' => $orderId,
                    'amount' => $orderRecord->order_total,
                    'name' => 'Order Package',
                    'products' => [
                        [
                            'id' => '1',
                            'quantity' => 1,
                            'name' => "訂單編號{$orderRecord->order_id}",
                            'price' => $orderRecord->order_total,
                        ],
                    ],
                ],
            ],
            'options' => [
                'display' => [
                    'locale' => 'zh_TW',
                ],
            ],
            'redirectUrls' => $redirectUrls,
        ];

        Log::info('獲得訂單資料，內容如下：', $order);

        try {
            $linePayResponse = $this->linePayService->reserve($order);

            // 增加日志记录以检查 linePayResponse 的结构
            Log::info('LINE Pay Response:', ['response' => $linePayResponse]);

            if (isset($linePayResponse['info']['paymentUrl']['web'])) {
                $paymentUrl = $linePayResponse['info']['paymentUrl']['web'];
                return redirect($paymentUrl);
            } else {
                throw new \Exception('Payment URL not found in LINE Pay response');
            }
        } catch (\Exception $e) {
            // 记录更多详细的错误信息
            Log::error('Error during LINE Pay request:', [
                'error' => $e->getMessage(),    
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['status' => 'error', 'message' => '付款请求失败'], 500);
        }
    }
}