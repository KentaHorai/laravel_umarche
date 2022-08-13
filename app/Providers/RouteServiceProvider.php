<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * This is used by Laravel authentication to redirect users after login.
     *
     * @var string
     */
    public const HOME = '/dashboard';
    public const OWNER_HOME = '/owner/dashboard';
    public const ADMIN_HOME = '/admin/dashboard';


    /**
     * The controller namespace for the application.
     *
     * When present, controller route declarations will automatically be prefixed with this namespace.
     *
     * @var string|null
     */
    // protected $namespace = 'App\\Http\\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        $this->configureRateLimiting();

        //ルート情報は大きく分けて2種類(apiかweb)
        //Laravelでview側を表示してリクエストレスポンスを返す(web)
        //フロント側を全てJavaScriptで作る場合(api)
        $this->routes(function () {
            Route::prefix('api')
                ->middleware('api')
                ->namespace($this->namespace)
                ->group(base_path('routes/api.php'));

            /*Route::middleware('web')
                ->namespace($this->namespace)
                ->group(base_path('routes/web.php'));*/

            //adminのルート情報
            Route::prefix('admin')//prefixメソッドを使用して、グループ内の各ルートに特定のURIをプレフィックスとして付ける
                //Route::get('users', function () {
                    // /admin/usersのURLに一致
                //});
                ->as('admin.')//別名を付ける
                //ミドルウェア(web)がroutes/admin.php内の全ての処理で実装される
                ->middleware('web')//グループを定義する前にmiddlewareメソッドを使用することで、ミドルウェアをグループ内の全てのルートに割り当てる
                ->namespace($this->namespace)
                ->group(base_path('routes/admin.php'));//routes/admin.phpの全てのURLに適用する

            //ownerのルート情報
            Route::prefix('owner')
                ->as('owner.')
                ->middleware('web')
                ->namespace($this->namespace)
                ->group(base_path('routes/owner.php'));

            //userのルート情報
            Route::prefix('/')
                ->as('user.')
                ->middleware('web')
                ->namespace($this->namespace)
                ->group(base_path('routes/web.php'));

        });
    }

    /**
     * Configure the rate limiters for the application.
     *
     * @return void
     */
    protected function configureRateLimiting()
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by(optional($request->user())->id ?: $request->ip());
        });
    }
}
