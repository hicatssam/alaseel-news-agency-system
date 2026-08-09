<?php

namespace App\Providers;

use App\Models\Advertisement;
use App\Models\Category;
use App\Models\LiveStream;
use App\Models\Notification;
use App\Models\Setting;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Schema::defaultStringLength(191);

        /*
        |--------------------------------------------------------------------------
        | إعدادات الموقع
        |--------------------------------------------------------------------------
        |
        | مشاركة إعدادات الموقع مع جميع الواجهات، حتى تكون متاحة داخل
        | الصفحات التي تستخدم layouts.app.
        |
        */

        View::composer('*', function ($view) {
            $siteSettings = cache()->remember(
                'site_settings',
                300,
                function () {
                    return Setting::query()
                        ->pluck('value', 'key')
                        ->toArray();
                }
            );

            $view->with('siteSettings', $siteSettings);
        });

        /*
        |--------------------------------------------------------------------------
        | بيانات الواجهة العامة
        |--------------------------------------------------------------------------
        */

        View::composer('layouts.app', function ($view) {
            $navCategories = Category::query()
                ->active()
                ->root()
                ->where('show_in_header', true)
                ->with([
                    'children' => function ($query) {
                        $query
                            ->active()
                            ->orderBy('sort_order')
                            ->orderBy('name');
                    },
                ])
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get();

            $footerCategories = Category::query()
                ->forFooter()
                ->limit(10)
                ->get();

            $activeStream = LiveStream::active();

            /*
            |--------------------------------------------------------------------------
            | إعلانات المواضع المشتركة
            |--------------------------------------------------------------------------
            |
            | header: أعلى صفحات الموقع
            | footer: أسفل صفحات الموقع
            | popup: نافذة منبثقة
            |
            */

            $layoutAds = Advertisement::query()
                ->active()
                ->whereIn('position', [
                    'header',
                    'footer',
                    'popup',
                ])
                ->latest()
                ->get()
                ->groupBy('position');

            $headerAds = $layoutAds->get(
                'header',
                collect()
            );

            $footerAds = $layoutAds->get(
                'footer',
                collect()
            );

            $popupAds = $layoutAds->get(
                'popup',
                collect()
            );

            $view->with(compact(
                'navCategories',
                'footerCategories',
                'activeStream',
                'headerAds',
                'footerAds',
                'popupAds'
            ));
        });

      

        View::composer('layouts.admin', function ($view) {
            $adminUnreadCount = Notification::query()
                ->whereNull('read_at')
                ->count();

            $adminRecentNotifications = Notification::query()
                ->latest()
                ->limit(8)
                
                ->get();

            $view->with(compact(
                'adminUnreadCount',
                'adminRecentNotifications'
            ));
        });
    }
}