<?php

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\VideoController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\LiveStreamController;

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ArticleController as AdminArticleController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\TagController;
use App\Http\Controllers\Admin\JournalistController;
use App\Http\Controllers\Admin\AdvertisementController;
use App\Http\Controllers\Admin\VideoController as AdminVideoController;
use App\Http\Controllers\Admin\MediaLibraryController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\ContactMessageController;
use App\Http\Controllers\Admin\NewsletterController;
use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\CommentController;
use App\Http\Controllers\Admin\LiveStreamController as AdminLiveStreamController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\ProfileController;

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])
        ->name('login');

    Route::post('/login', [LoginController::class, 'login']);
});

Route::post('/logout', [LoginController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

/*
|--------------------------------------------------------------------------
| Language Switcher
|--------------------------------------------------------------------------
*/

Route::get('/language/{locale}', function (string $locale) {
    if (in_array($locale, ['ar', 'en', 'fr'], true)) {
        session(['locale' => $locale]);

        App::setLocale($locale);
    }

    return redirect()->back();
})->name('language.switch');

/*
|--------------------------------------------------------------------------
| Public Website
|--------------------------------------------------------------------------
*/

Route::get('/', [HomeController::class, 'index'])
    ->name('home');

Route::post('/newsletter/subscribe', [HomeController::class, 'subscribeNewsletter'])
    ->name('newsletter.subscribe');

Route::get('/newsletter/unsubscribe', [HomeController::class, 'unsubscribeNewsletter'])
    ->name('newsletter.unsubscribe');

Route::get('/search', [SearchController::class, 'index'])
    ->name('search');

Route::get('/videos', [VideoController::class, 'index'])
    ->name('videos.index');

Route::get('/videos/{slug}', [VideoController::class, 'show'])
    ->name('videos.show');

Route::get('/contact', [ContactController::class, 'index'])
    ->name('contact');

Route::post('/contact', [ContactController::class, 'store'])
    ->name('contact.store');

Route::get('/category/{slug}', [CategoryController::class, 'show'])
    ->name('categories.show');

Route::get('/article/{slug}', [ArticleController::class, 'show'])
    ->name('articles.show');

Route::post('/article/{article}/comment', [ArticleController::class, 'postComment'])
    ->name('articles.comment');

Route::get('/live', [LiveStreamController::class, 'show'])
    ->name('live');

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/

Route::prefix('admin')
    ->name('admin.')
    ->middleware('auth')
    ->group(function () {

        /*
        |--------------------------------------------------------------------------
        | جميع مستخدمي لوحة التحكم
        |--------------------------------------------------------------------------
        */

        Route::get('/', [DashboardController::class, 'index'])
            ->name('dashboard');

        Route::get('profile', [ProfileController::class, 'show'])
            ->name('profile.show');

        Route::put('profile', [ProfileController::class, 'update'])
            ->name('profile.update');

        /*
        |--------------------------------------------------------------------------
        | الصحفي + المحرر + المدير العام
        |--------------------------------------------------------------------------
        */

        Route::middleware('role:journalist,editor,super-admin')
            ->group(function () {

                Route::resource('articles', AdminArticleController::class);

                Route::patch(
                    'articles/{article}/status',
                    [AdminArticleController::class, 'updateStatus']
                )->name('articles.status');

                Route::get(
                    'articles/{article}/revisions',
                    [AdminArticleController::class, 'revisions']
                )->name('articles.revisions');

                Route::get(
                    'media',
                    [MediaLibraryController::class, 'index']
                )->name('media.index');

                Route::get(
    'media-picker',
    [MediaLibraryController::class, 'picker']
)->name('media.picker');

                Route::post(
                    'media',
                    [MediaLibraryController::class, 'store']
                )->name('media.store');

                Route::patch(
                    'media/{mediaFile}',
                    [MediaLibraryController::class, 'update']
                )->name('media.update');

                Route::delete(
                    'media/{mediaFile}',
                    [MediaLibraryController::class, 'destroy']
                )->name('media.destroy');
            });

        /*
        |--------------------------------------------------------------------------
        | المحرر + المدير العام
        |--------------------------------------------------------------------------
        */

        Route::middleware('role:editor,super-admin')
            ->group(function () {

                Route::resource(
                    'categories',
                    AdminCategoryController::class
                );

                Route::resource('tags', TagController::class);

                Route::resource(
                    'journalists',
                    JournalistController::class
                );

                Route::resource(
                    'advertisements',
                    AdvertisementController::class
                );

                Route::resource(
                    'videos',
                    AdminVideoController::class
                );

                Route::get(
                    'comments',
                    [CommentController::class, 'index']
                )->name('comments.index');

                Route::patch(
                    'comments/{comment}/status',
                    [CommentController::class, 'updateStatus']
                )->name('comments.status');

                Route::delete(
                    'comments/{comment}',
                    [CommentController::class, 'destroy']
                )->name('comments.destroy');

                Route::resource(
                    'live-streams',
                    AdminLiveStreamController::class
                );

                Route::patch(
                    'live-streams/{liveStream}/toggle',
                    [AdminLiveStreamController::class, 'toggle']
                )->name('live-streams.toggle');
            });

        /*
        |--------------------------------------------------------------------------
        | المدير العام فقط
        |--------------------------------------------------------------------------
        */

        Route::middleware('role:super-admin')
            ->group(function () {

                /*
                |--------------------------------------------------------------------------
                | إدارة المستخدمين
                |--------------------------------------------------------------------------
                */

                Route::patch(
                    'users/{user}/toggle-status',
                    [UserController::class, 'toggleStatus']
                )->name('users.toggle-status');

                Route::patch(
                    'users/{user}/password',
                    [UserController::class, 'updatePassword']
                )->name('users.update-password');

                Route::resource('users', UserController::class);

                /*
                |--------------------------------------------------------------------------
                | الإعدادات
                |--------------------------------------------------------------------------
                */

                Route::get(
                    'settings',
                    [SettingController::class, 'index']
                )->name('settings.index');

                Route::post(
                    'settings',
                    [SettingController::class, 'update']
                )->name('settings.update');

                /*
                |--------------------------------------------------------------------------
                | رسائل التواصل
                |--------------------------------------------------------------------------
                */

                Route::get(
                    'contact',
                    [ContactMessageController::class, 'index']
                )->name('contact.index');

                Route::get(
                    'contact/{contactMessage}',
                    [ContactMessageController::class, 'show']
                )->name('contact.show');

                Route::delete(
                    'contact/{contactMessage}',
                    [ContactMessageController::class, 'destroy']
                )->name('contact.destroy');

                /*
                |--------------------------------------------------------------------------
                | النشرة البريدية
                |--------------------------------------------------------------------------
                */

                Route::get(
                    'newsletter',
                    [NewsletterController::class, 'index']
                )->name('newsletter.index');

                Route::delete(
                    'newsletter/{newsletterSubscriber}',
                    [NewsletterController::class, 'destroy']
                )->name('newsletter.destroy');

                /*
                |--------------------------------------------------------------------------
                | سجل النشاطات
                |--------------------------------------------------------------------------
                */

                Route::get(
                    'activity-logs',
                    [ActivityLogController::class, 'index']
                )->name('activity-logs.index');


                
                /*
                |--------------------------------------------------------------------------
                | الإشعارات
                |--------------------------------------------------------------------------
                */

                Route::get(
    '/notifications/check-new',
    [NotificationController::class, 'checkNew']
)->name('notifications.check-new');

                Route::get(
                    'notifications',
                    [NotificationController::class, 'index']
                )->name('notifications.index');

                Route::post(
                    'notifications/read-all',
                    [NotificationController::class, 'markAllRead']
                )->name('notifications.read-all');

                Route::delete(
                    'notifications/clear-read',
                    [NotificationController::class, 'destroyRead']
                )->name('notifications.clear-read');

                Route::post(
                    'notifications/{notification}/read',
                    [NotificationController::class, 'markRead']
                )->name('notifications.read');

                Route::delete(
                    'notifications/{notification}',
                    [NotificationController::class, 'destroy']
                )->name('notifications.destroy');
            });
    });