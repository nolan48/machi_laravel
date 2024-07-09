<?php
namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use App\Http\Middleware\JwtMiddleware;
class RouteServiceProvider extends ServiceProvider
{


    /**
     * 这个命名空间被应用到你的控制器路由。
     * 此外，它在 URL 生成器中设置为根命名空间。
     *
     * @var string
     */
    protected $namespace = 'App\\Http\\Controllers';

    /**
     * 引导任何应用程序服务。
     */
    public function boot(): void
    {
                // 注册路由中间件
                Route::aliasMiddleware('auth.jwt', JwtMiddleware::class);
        $this->configureRateLimiting();

        $this->routes(function () {
            Route::middleware('api')
                ->namespace($this->namespace)
                ->group(base_path('routes/api.php'));
                
        });
    }
}