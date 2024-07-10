<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
class LinePayService
{
    protected $client;
    protected $channelId;
    protected $channelSecret;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => 'https://api-pay.line.me/',
            'verify' => false, // 禁用 SSL 驗證，僅在開發環境中使用
        ]);
        $this->channelId = env('LINE_PAY_CHANNEL_ID');
        $this->channelSecret = env('LINE_PAY_CHANNEL_SECRET');
    }

    public function reserve($order)
    {
        try {
            $response = $this->client->post('/v3/payments/request', [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'X-LINE-ChannelId' => $this->channelId,
                    'X-LINE-ChannelSecret' => $this->channelSecret,
                ],
                'json' => $order,
            ]);

            return json_decode($response->getBody(), true);
        } catch (\Exception $e) {
            Log::error('Error during LINE Pay request: ', ['error' => $e->getMessage()]);
            throw $e;
        }
    }
}
