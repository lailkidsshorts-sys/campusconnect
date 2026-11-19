<?php

namespace App\Providers;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
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
        Schema::defaultStringLength(191);

        // Force HTTPS in production and when behind Cloudflare/reverse proxy
        if (config('app.env') === 'production' || request()->header('X-Forwarded-Proto') === 'https' || request()->isSecure()) {
            URL::forceScheme('https');
        }

        // Auto-run migrations and seeds on cloud server if database is empty
        try {
            if (! Schema::hasTable('users')) {
                Artisan::call('migrate', ['--force' => true]);
                Artisan::call('db:seed', ['--force' => true]);
            }
        } catch (\Throwable $e) {
            Log::warning('Automatic migration check skipped or failed: ' . $e->getMessage());
        }
    }
}
