<?php

namespace App\Http\Middleware;

use Closure;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Illuminate\Support\Facades\Log;

class JwtMiddleware
{
    public function handle($request, Closure $next)
    {
        Log::info('JwtMiddleware handle method called');
        
        // 检查请求头中的 Authorization 字段
        $authHeader = $request->header('Authorization');
        if (!$authHeader) {
            Log::error('Authorization header not found');
            return response()->json(['error' => 'Authorization header not found'], 401);
        }

        // 确认令牌格式
        if (strpos($authHeader, 'Bearer ') !== 0) {
            Log::error('Authorization header format is invalid');
            return response()->json(['error' => 'Authorization header format is invalid'], 401);
        }

        try {
            $token = JWTAuth::parseToken();
            $user = $token->authenticate();
            if (!$user) {
                Log::error('User not found for token');
                return response()->json(['error' => 'User not found'], 404);
            }
        } catch (TokenExpiredException $e) {
            Log::error('Token has expired: ' . $e->getMessage());
            return response()->json(['error' => 'Token has expired'], 401);
        } catch (TokenInvalidException $e) {
            Log::error('Token is invalid: ' . $e->getMessage());
            return response()->json(['error' => 'Token is invalid'], 401);
        } catch (JWTException $e) {
            Log::error('Token is not provided: ' . $e->getMessage());
            return response()->json(['error' => 'Token is not provided'], 401);
        }

        return $next($request);
    }
}
