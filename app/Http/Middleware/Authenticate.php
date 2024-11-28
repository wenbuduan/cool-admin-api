<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Support\Facades\Auth;

class Authenticate extends Middleware
{
    public function handle($request, Closure $next, ...$guards)
    {
        $http_status_code = 200;

        if (!Auth::check()) {
            //return response('Unauthorized.', 401);
            // 修改非授权条件下的返回格式 确保所有api接口返回格式统一
            // 小程序收到401自动弹出登录页面
            return response()->json(
                [
                    'code' => 401,
                    'message' => 'Unauthorized.',
                    'data' => null
                ],
                $http_status_code,
                [
                    'charset' => 'utf-8',
                    'Access-Control-Allow-Origin' => '*',
                    'Access-Control-Allow-Credentials' => 'true',
                ],
                JSON_UNESCAPED_UNICODE
            );
        }

        return $next($request);
    }
}
