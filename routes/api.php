<?php


use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Middleware\JwtMiddleware;
use App\Models\User;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\ProductFavoriteController;
use App\Http\Controllers\CustomizeController;
use App\Http\Controllers\CartItemController;
use App\Http\Controllers\OrderController;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\CommentController;


    //會員系統
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



    //商品載入
    Route::get('products', [ProductController::class, 'index']);
    Route::get('products/{id}', [ProductController::class, 'show']);
    
    //我的最愛
    Route::get('product-favorite', [ProductFavoriteController::class, 'index'])->middleware(JwtMiddleware::class);
    Route::put('product-favorite/{id}', [ProductFavoriteController::class, 'store'])->middleware(JwtMiddleware::class);
    Route::delete('product-favorite/{id}', [ProductFavoriteController::class, 'destroy'])->middleware(JwtMiddleware::class);

    //課程用
    Route::get('course', [CourseController::class, 'index']);
    Route::get('course/{id}', [CourseController::class, 'show']);

    //客制用
    Route::post('/upload-customize', [CustomizeController::class, 'uploadCustomize'])->middleware('auth');

    //購物車
    Route::middleware([JwtMiddleware::class])->group(function () {
        Route::get('/cart', [CartItemController::class, 'index']);
        Route::post('/cart', [CartItemController::class, 'store']);
        Route::put('/cart', [CartItemController::class, 'update']);
        Route::delete('/cart', [CartItemController::class, 'destroy']);
    });
    
    //訂單
    Route::middleware([JwtMiddleware::class])->group(function () {
        Route::get('/order/order-items', [OrderController::class, 'getOrderItems']);
        Route::get('/order/better', [OrderController::class, 'getOrders']);
        Route::post('/order', [OrderController::class, 'createOrder']);
    });

    //Line付款
    Route::middleware([JwtMiddleware::class])->group(function () {
        Route::get('/line-pay/reserve', [PaymentController::class, 'reserve']);
    });


    //分類
    Route::get('/category', [CategoryController::class, 'index']);

    //文章
    Route::prefix('articles')->group(function () {
        Route::post('/publish', [ArticleController::class, 'publish']);
        Route::post('/upload', [ArticleController::class, 'upload']);
        Route::get('/', [ArticleController::class, 'getArticles']);
        Route::get('/filtered', [ArticleController::class, 'getFilteredArticles']);
        Route::get('/{id}', [ArticleController::class, 'getArticleById']);
    });
    
    Route::prefix('comments')->group(function () {
        Route::post('/commit', [CommentController::class, 'commit']);
        Route::get('/{id}', [CommentController::class, 'getCommentsByArticleId']);
    });