<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Advertisement;
use App\Models\Category;
use App\Models\LiveStream;
use App\Models\Notification;
use App\Models\Setting;
use Illuminate\Support\Facades\Schema;
class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {



    Schema::defaultStringLength(191);

        View::composer('layouts.app', function ($view) {
            // Navigation / footer categories
            $navCategories    = Category::forHeader()->limit(8)->get();
            $footerCategories = Category::forFooter()->limit(10)->get();
            $activeStream     = LiveStream::active();

            // DB settings — cached 5 min so every page doesn't hit the DB
            $settings = cache()->remember('site_settings', 300, function () {
                return Setting::all()->pluck('value', 'key')->toArray();
            });

            // Ads for positions that live in the shared layout
            $headerAds = Advertisement::active()->forPosition('header')->get();
            $footerAds = Advertisement::active()->forPosition('footer')->get();

            $view->with(compact(
                'navCategories', 'footerCategories', 'activeStream',
                'settings', 'headerAds', 'footerAds'
            ));
        });

        View::composer('layouts.admin', function ($view) {
            $adminUnreadCount        = Notification::whereNull('read_at')->count();
            $adminRecentNotifications = Notification::latest()->limit(8)->get();
            $view->with(compact('adminUnreadCount', 'adminRecentNotifications'));
        });
    }
}
