<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // 注册默认的 domain 值
        $this->app->instance('domain', 'user');
        
        // 注册 vuedata 实例
        $this->app->instance('vuedata', []);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // 定义 @vuedata Blade 指令
        Blade::directive('vuedata', function ($expression) {
            return "<?php app()->instance('vuedata', array_merge(app('vuedata'), $expression)); ?>";
        });
    }
}
