<?php
namespace App\Http\Middleware;

use Closure;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Illuminate\Support\Facades\Log;
use App\Models\User;

class JwtMiddleware
{
    public function handle($request, Closure $next)
    {
        Log::info('JwtMiddleware handle method called');

        $token = $request->cookie('accessToken');
        Log::info('Token from cookie: ' . $token);

        if (!$token) {
            Log::error('accessToken cookie not found');
            return response()->json(['error' => 'accessToken cookie not found'], 401);
        }

        try {
            JWTAuth::setToken($token);
            Log::info('Token set: ' . $token);

            $payload = JWTAuth::getPayload();
            Log::info('Token payload: ' . json_encode($payload));

            $sub = $payload->get('sub');
            Log::info('Token subject (sub): ' . $sub);

            $user = User::find($sub);
            Log::info('User found with User::find: ' . json_encode($user));

            if (!$user) {
                Log::error('User not found for sub: ' . $sub);
                return response()->json(['error' => 'User not found'], 404);
            }

            $request->setUserResolver(function () use ($user) {
                return $user;
            });
        } catch (TokenExpiredException $e) {
            Log::error('Token has expired: ' . $e->getMessage());
            return response()->json(['error' => 'Token has expired'], 401);
        } catch (TokenInvalidException $e) {
            Log::error('Token is invalid: ' . $e->getMessage());
            return response()->json(['error' => 'Token is invalid'], 401);
        } catch (JWTException $e) {
            Log::error('JWT Exception: ' . $e->getMessage());
            return response()->json(['error' => 'A token is required'], 401);
        }

        return $next($request);
    }
}
