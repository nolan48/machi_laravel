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

    protected function generateSignature($uri, $requestBody, $nonce)
    {
        $data = $this->channelSecret . $uri . json_encode($requestBody) . $nonce;
        return base64_encode(hash_hmac('sha256', $data, $this->channelSecret, true));
    }

    public function reserve($order)
    {
        try {
            Log::info('Sending request to LINE Pay', ['order' => $order]);

            $nonce = time(); // 使用时间戳作为nonce
            $signature = $this->generateSignature('/v3/payments/request', $order, $nonce);

            $response = $this->client->post('/v3/payments/request', [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'X-LINE-ChannelId' => $this->channelId,
                    'X-LINE-Authorization-Nonce' => $nonce,
                    'X-LINE-Authorization' => $signature,
                ],
                'json' => $order,
            ]);

            $responseBody = $response->getBody();
            Log::info('Received response from LINE Pay', ['response' => (string) $responseBody]);

            return json_decode($responseBody, true);
        } catch (\Exception $e) {
            Log::error('Error during LINE Pay request: ', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            throw $e;
        }
    }
}
