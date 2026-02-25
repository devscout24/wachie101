<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Cache;
use App\Models\SystemSetting;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Register system settings as a singleton
        $this->app->singleton('system.settings', function () {
            // Try to get from cache, otherwise fetch from DB
            return Cache::rememberForever('system_settings', function () {
                return SystemSetting::first();
            });
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Share system settings with all views
        $this->shareSettingsWithViews();

        // Listen for changes to clear cache automatically
        $this->registerCacheInvalidation();
        View::composer('*', function ($view) {
        $settings = Cache::remember('system_settings', 3600, function () {
            return SystemSetting::first();
        });
        $view->with('systemSettings', $settings);
    });
    }

    /**
     * Share system settings with all views.
     */
    protected function shareSettingsWithViews(): void
    {
        View::composer('*', function ($view) {
            $settings = app('system.settings');
            $view->with('systemSettings', $settings);
        });
    }

    /**
     * Clear cache automatically when system settings change.
     */
    protected function registerCacheInvalidation(): void
    {
        $clearCache = function ($settings) {
            // Clear cache
            Cache::forget('system_settings');

            // Remove singleton instance so it reloads next time
            if ($this->app->bound('system.settings')) {
                $this->app->forgetInstance('system.settings');
            }
        };

        // Listen to Eloquent events
        SystemSetting::created($clearCache);
        SystemSetting::updated($clearCache);
        SystemSetting::deleted($clearCache);
    }
}
