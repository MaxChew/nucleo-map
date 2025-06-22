<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RegisterGlobal
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $domain = 'user'): Response
    {
        // 将 domain 值注册到服务容器中
        app()->instance('domain', $domain);
        
        // 也可以通过配置或其他方式设置相关的 domain 信息
        config(['app.current_domain' => $domain]);
        
        return $next($request);
    }
} 