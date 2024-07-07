<?php


use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Middleware\JwtMiddleware;
use App\Models\User;

use Tymon\JWTAuth\Facades\JWTAuth;
Route::get('generate-token', function () {
    // 查找一个现有的用户
    $user = User::first();
    
    // 如果找不到用户，返回错误信息
    if (!$user) {
        return response()->json(['error' => 'No user found'], 404);
    }
    
    // 生成 JWT 令牌
    $token = JWTAuth::fromUser($user);
    
    // 返回生成的令牌
    return response()->json(['token' => $token]);
});


    Route::post('auth/login', [AuthController::class, 'login']);
    Route::post('auth/logout', [AuthController::class, 'logout']);
    Route::get('auth/check', [AuthController::class, 'checkAuth']);
    Route::post('auth/refresh', [AuthController::class, 'refresh']);
    Route::post('users/register', [AuthController::class, 'register']);
    
    Route::middleware(JwtMiddleware::class)->group(function () {
        Route::get('/protected-route', function () {
            return response()->json(['message' => 'This is a protected route']);
        });
    });





