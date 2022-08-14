<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Support\Facades\Route;
class Authenticate extends Middleware
{
    protected $user_route = 'user.login';
    protected $owner_route = 'owner.login';
    protected $admin_route = 'admin.login';


    /**
     * Get the path the user should be redirected to when they are not authenticated.
     * ユーザーが認証されていない場合のリダイレクト処理
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
        if (! $request->expectsJson()) {//Jsonではなかったら
            if(Route::is('owner.*')){//オーナー関連のURLであれば
                return route($this->owner_route);//オーナーのログイン画面に飛ばす
            }elseif(Route::is('admin.*')){
                return route($this->admin_route);
            }else{
                return route($this->user_route);
            }
            //return route('login');
        }
    }
}
