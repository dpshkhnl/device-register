<?php

namespace App\Providers;

use App\Models\FooterLink;
use App\Models\SystemSetting;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    private const LAYOUT_CACHE_KEY = 'layout:settings-footer:v1';

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
        // Settings + footer links used to be two queries on every request (including every IMEI check).
        // Load them lazily, only when a view renders, from cache; admin edits clear the cache.
        $forgetLayout = fn () => Cache::forget(self::LAYOUT_CACHE_KEY);
        foreach ([SystemSetting::class, FooterLink::class] as $model) {
            $model::saved($forgetLayout);
            $model::deleted($forgetLayout);
        }

        $layout = null;
        View::composer('*', function ($view) use (&$layout) {
            $layout ??= Cache::rememberForever(self::LAYOUT_CACHE_KEY, fn () => [
                'settings' => SystemSetting::first(),
                'footerLinks' => FooterLink::where('is_active', true)
                    ->orderBy('group')
                    ->orderBy('sort_order')
                    ->get()
                    ->groupBy('group'),
            ]);
            foreach ($layout as $key => $value) {
                if (! array_key_exists($key, $view->getData())) {
                    $view->with($key, $value);
                }
            }
        });

        // Only actual lookups count (plain page views don't), per user or per IP for guests.
        RateLimiter::for('imei-lookup', function (Request $request) {
            if (! $request->filled('imei')) {
                return Limit::none();
            }

            return Limit::perMinute(config('imei.rate_limit'))
                ->by($request->user()?->id ? 'user:'.$request->user()->id : 'ip:'.$request->ip());
        });
    }
}
