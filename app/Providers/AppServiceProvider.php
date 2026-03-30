<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Cấu hình Carbon hiển thị tiếng Việt (diffForHumans, v.v.)
        \Carbon\Carbon::setLocale('vi');

        // --- 1. Cấu hình Timeout ---
        // PHP Execution Timeout: 120 giây
        // set_time_limit(120);
        
        // Laravel HTTP Client Timeout (outgoing request): 120 giây mặc định
        \Illuminate\Support\Facades\Http::globalOptions(['timeout' => 120]);

        // Đăng ký Blueprint macro — dùng $table->baseColumns() trong migration
        \Illuminate\Database\Schema\Blueprint::macro('baseColumns', function () {
            $this->id();
            $this->ulid('unitcode')->unique();
            $this->boolean('is_deleted')->default(false)->index();
        });

        \Illuminate\Support\Facades\RateLimiter::for('web', function (\Illuminate\Http\Request $request) {
            return \Illuminate\Cache\RateLimiting\Limit::perMinute(120)->by($request->session()->get('_token') ?: $request->ip());
        });

        \Illuminate\Support\Facades\RateLimiter::for('auth', function (\Illuminate\Http\Request $request) {
            return \Illuminate\Cache\RateLimiting\Limit::perMinute(5)->by($request->input('email') . $request->ip());
        });

        \Illuminate\Support\Facades\RateLimiter::for('downloads', function (\Illuminate\Http\Request $request) {
            return \Illuminate\Cache\RateLimiting\Limit::perMinute(10)->by($request->user()?->id ?: $request->ip());
        });

        // --- Audit Logs cho Authentication ---
        \Illuminate\Support\Facades\Event::listen(\Illuminate\Auth\Events\Login::class, function (\Illuminate\Auth\Events\Login $event) {
            \App\Services\AuditLogService::log('login', $event->user, null, null, $event->user->id);
        });

        \Illuminate\Support\Facades\Event::listen(\Illuminate\Auth\Events\Logout::class, function (\Illuminate\Auth\Events\Logout $event) {
            if ($event->user) {
                \App\Services\AuditLogService::log('logout', $event->user, null, null, $event->user->id);
            }
        });
    }
}
