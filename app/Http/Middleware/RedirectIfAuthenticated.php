<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    //config\auth.phpと一致させる必要がある
    private const GUARD_USER = 'users';
    private const GUARD_OWNER = 'owners';
    private const GUARD_ADMIN = 'admin';

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @param  string|null  ...$guards
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, ...$guards)
    {
        /*
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                return redirect(RouteServiceProvider::HOME);
            }
        }
        */

        //ログイン済みのユーザがアクセスしてきたときのリダイレクト処理
        if(Auth::guard(self::GUARD_USER)->check() && $request->routeIs('user.*')){//ガード設定対象のユーザか && 受信リクエストが名前付きルートに一致するか
            return redirect(RouteServiceProvider::HOME);
        }//Auth::guard('admin')・・・adminとして認証している
        //Auth::check()・・・現在のユーザが認証済みか判定

        if(Auth::guard(self::GUARD_OWNER)->check() && $request->routeIs('owner.*')){
            return redirect(RouteServiceProvider::OWNER_HOME);
        }

        if(Auth::guard(self::GUARD_ADMIN)->check() && $request->routeIs('admin.*')){
            return redirect(RouteServiceProvider::ADMIN_HOME);
        }

        return $next($request);
    }
}
