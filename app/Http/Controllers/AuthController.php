<?php
// app/Http/Controllers/AuthController.php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;


class AuthController extends Controller
{
    public function checkAuth(Request $request)
    {
        try {

            $token = $request->cookie('accessToken');

            if (!$token) {
                Log::info('Token not found in cookie');
                return response()->json([
                    'isAuth' => false,
                    'userData' => [
                        'user_id' => 0,
                        'user_name' => '',
                        'user_nickname' => '',
                        'user_account' => '',
                        'user_email' => '',
                        'user_gender' => '',
                        'user_birthday' => '',
                        'user_phone' => '',
                        'user_address' => '',
                        'google_uid' => '',
                        'line_uid' => ''
                    ]
                ]);
            }

            Log::info('Token found: ' . $token);
            $request->headers->set('Authorization', 'Bearer ' . $token);
            
            // 增加调试日志
            JWTAuth::setToken($token);
            $payload = JWTAuth::getPayload();
            $userId = $payload->get('sub');
            Log::info('User ID from token: ' . $userId);
    
            // 使用用户ID查找用户
            $user = User::where('user_id', $userId)->first();
            if (!$user) {
                Log::error('User not found for user_id: ' . $userId);
                return response()->json(['isAuth' => false, 'userData' => []]);
            }
    

            Log::info('User authenticated: ' . $user->user_id);
            return response()->json([
                'status' => 'success',
                'data' => [
                    'user' => [
                        'user_id' => $user->user_id,
                        'user_name' => $user->user_name,
                        'user_nickname' => $user->user_nickname,
                        'user_account' => $user->user_account,
                        'user_email' => $user->user_email,
                        'user_gender' => $user->user_gender,
                        'user_birthday' => $user->user_birthday,
                        'user_phone' => $user->user_phone,
                        'user_address' => $user->user_address,
                        'google_uid' => $user->google_uid,
                        'line_uid' => $user->line_uid
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('General Exception: ' . $e->getMessage());
            return response()->json([
                'error' => 'Internal Server Error',
            ], 500);
        }
    }

    public function login(Request $request)
    {
        $credentials = [
            'email' => $request->input('email'),
            'password' => $request->input('password'),
        ];  
        
        Log::info('Credentials received', $credentials);
        
        // 检查从前端传来的数据是否完整
        if (!$credentials['email'] || !$credentials['password']) {
            Log::warning('Incomplete credentials', $credentials);
            return response()->json(['status' => 'fail', 'data' => null]);
        }
    
        try {
            // 查找用户
            $user = User::where('user_email', $credentials['email'])->first();
            Log::info('User lookup', ['user' => $user]);
    
            if (!$user) {
                Log::warning('User not found', ['user_email' => $credentials['email']]);
                return response()->json(['status' => 'error', 'message' => '用户不存在']);
            }
    
            // 验证密码
            if (!Hash::check($credentials['password'], $user->user_password)) {
                Log::warning('Password mismatch', ['user_email' => $credentials['email']]);
                return response()->json(['status' => 'error', 'message' => '密码错误']);
            }

            // 生成存取令牌(access token)
            try {
                $token = JWTAuth::attempt(['user_email' => $credentials['email'], 'password' => $credentials['password']]);
                if (!$token) {
                    Log::warning('JWT token generation failed', $credentials);
                    return response()->json(['status' => 'error', 'message' => '认证失败'], 401);
                }
            } catch (JWTException $e) {
                Log::error('JWT Exception: ' . $e->getMessage());
                return response()->json(['status' => 'error', 'message' => '无法创建令牌'], 500);
            }

            Log::info('User logged in successfully', ['user_id' => $user->user_id, 'token' => $token]);
    
            // 使用httpOnly cookie来让浏览器端储存access token
            return response()
                ->json([
                    'status' => 'success',
                    'data' => [
                        'token' => $token,
                        'user' => [
                            'user_id' => $user->user_id,
                            'user_name' => $user->user_name,
                            'user_nickname' => $user->user_nickname,
                            'user_account' => $user->user_account,
                            'user_email' => $user->user_email,
                            'user_gender' => $user->user_gender,
                            'user_birthday' => $user->user_birthday,
                            'user_phone' => $user->user_phone,
                            'user_address' => $user->user_address,
                            'google_uid' => $user->google_uid,
                            'line_uid' => $user->line_uid
                        ]
                    ]
                ])
                ->cookie('accessToken', $token, 360, null, null, false, true, true); 
        } catch (JWTException $e) {
            Log::error('JWT Exception: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => '无法创建令牌'], 500);
        } catch (\Exception $e) {
            Log::error('General Exception: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => '服务器内部错误'], 500);
        }
    }
    

    // 刷新 token 的方法
    public function refresh()
    {
        try {
            $newToken = JWTAuth::refresh(JWTAuth::getToken());
            return response()->json(['status' => 'success', 'token' => $newToken]);
        } catch (JWTException $e) {
            return response()->json(['status' => 'fail', 'message' => 'Could not refresh token'], 401);
        }
    }

    public function register(Request $request)
    {
        // Validate the request data
        $validator = Validator::make($request->all(), [
            'account' => 'required|string|max:255',
            'password' => 'required|string|min:3',
            'email' => 'required|string|email|max:255|unique:users,user_email',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => $request], 400);
        }

        // Create the new user
        try {
            $user = User::create([
                'user_account' => $request->account,
                'user_password' => Hash::make($request->password), // 使用 Bcrypt 加密密码
                'user_email' => $request->email,
                'user_createtime' => now(),
                'user_updatetime' => now(),
            ]);

            return response()->json(['status' => 'success', 'data' => null], 201);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => '帳號已存在'], 400);
        }
    }
}

