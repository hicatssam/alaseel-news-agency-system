<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class SettingController extends Controller
{
    /**
     * الإعدادات الافتراضية المطلوبة في النظام.
     */
    private const DEFAULT_SETTINGS = [
        'site_logo' => [
            'type' => 'image',
            'group' => 'general',
        ],
        'facebook_url' => [
            'type' => 'url',
            'group' => 'social',
        ],
        'instagram_url' => [
            'type' => 'url',
            'group' => 'social',
        ],
        'youtube_url' => [
            'type' => 'url',
            'group' => 'social',
        ],
        'twitter_url' => [
            'type' => 'url',
            'group' => 'social',
        ],
        'linkedin_url' => [
            'type' => 'url',
            'group' => 'social',
        ],
        'tiktok_url' => [
            'type' => 'url',
            'group' => 'social',
        ],
        'telegram_url' => [
            'type' => 'url',
            'group' => 'social',
        ],
        'whatsapp_url' => [
            'type' => 'url',
            'group' => 'social',
        ],
    ];

    /**
     * عرض صفحة الإعدادات.
     */
    public function index(): View
    {
        foreach (self::DEFAULT_SETTINGS as $key => $meta) {
            Setting::firstOrCreate(
                ['key' => $key],
                [
                    'value' => null,
                    'type' => $meta['type'],
                    'group' => $meta['group'],
                ]
            );
        }

        $settings = Setting::query()
            ->orderBy('group')
            ->orderBy('key')
            ->get()
            ->groupBy('group');

        return view('admin.settings.index', compact('settings'));
    }

    /**
     * تحديث إعدادات الموقع.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'settings' => [
                'nullable',
                'array',
            ],

            // -----------------------------------------------------------
            // عام (General)
            // -----------------------------------------------------------
            'settings.site_name' => [
                'nullable',
                'string',
                'max:255',
            ],
            'settings.site_tagline' => [
                'nullable',
                'string',
                'max:255',
            ],
            'settings.site_favicon' => [
                'nullable',
                'string',
                'max:2048',
            ],
            'settings.footer_text' => [
                'nullable',
                'string',
                'max:1000',
            ],

            // -----------------------------------------------------------
            // بيانات التواصل (Contact)
            // -----------------------------------------------------------
            'settings.site_email' => [
                'nullable',
                'email',
                'max:255',
            ],
            'settings.site_phone' => [
                'nullable',
                'string',
                'max:50',
            ],
            'settings.site_address' => [
                'nullable',
                'string',
                'max:500',
            ],

            // -----------------------------------------------------------
            // العرض والميزات (Display / Features)
            // -----------------------------------------------------------
            'settings.articles_per_page' => [
                'nullable',
                'integer',
                'min:1',
                'max:100',
            ],
            'settings.comments_enabled' => [
                'nullable',
            ],

            // -----------------------------------------------------------
            // روابط التواصل الاجتماعي (Social)
            // -----------------------------------------------------------
            'settings.facebook_url' => [
                'nullable',
                'url',
                'max:2048',
            ],
            'settings.instagram_url' => [
                'nullable',
                'url',
                'max:2048',
            ],
            'settings.youtube_url' => [
                'nullable',
                'url',
                'max:2048',
            ],
            'settings.twitter_url' => [
                'nullable',
                'url',
                'max:2048',
            ],
            'settings.linkedin_url' => [
                'nullable',
                'url',
                'max:2048',
            ],
            'settings.tiktok_url' => [
                'nullable',
                'url',
                'max:2048',
            ],
            'settings.telegram_url' => [
                'nullable',
                'url',
                'max:2048',
            ],
            'settings.whatsapp_url' => [
                'nullable',
                'url',
                'max:2048',
            ],

            'site_logo' => [
                'nullable',
                'file',
                'mimes:png,jpg,jpeg,webp,svg',
                'max:4096',
            ],

            'remove_site_logo' => [
                'nullable',
                'boolean',
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | حفظ روابط التواصل وبقية الإعدادات
        |--------------------------------------------------------------------------
        */

        foreach (($validated['settings'] ?? []) as $key => $value) {
            if ($key === 'site_logo') {
                continue;
            }

            $cleanValue = is_string($value)
                ? trim($value)
                : $value;

            $defaultMeta = self::DEFAULT_SETTINGS[$key] ?? null;

            $existingSetting = Setting::query()
                ->where('key', $key)
                ->first();

            Setting::updateOrCreate(
                ['key' => $key],
                [
                    'value' => $cleanValue,
                    'type' => $existingSetting?->type
                        ?? $defaultMeta['type']
                        ?? 'text',
                    'group' => $existingSetting?->group
                        ?? $defaultMeta['group']
                        ?? 'general',
                ]
            );
        }

        /*
        |--------------------------------------------------------------------------
        | حذف الشعار الحالي
        |--------------------------------------------------------------------------
        */

        $logoSetting = Setting::query()
            ->where('key', 'site_logo')
            ->first();

        $oldLogo = $logoSetting?->value;

        if ($request->boolean('remove_site_logo')) {
            $this->deleteManagedLogo($oldLogo);

            Setting::updateOrCreate(
                ['key' => 'site_logo'],
                [
                    'value' => null,
                    'type' => 'image',
                    'group' => 'general',
                ]
            );

            $oldLogo = null;
        }

        /*
        |--------------------------------------------------------------------------
        | رفع الشعار الجديد
        |--------------------------------------------------------------------------
        */

        if ($request->hasFile('site_logo')) {
            $this->deleteManagedLogo($oldLogo);

            $path = $request
                ->file('site_logo')
                ->store('settings/logos', 'public');

            Setting::updateOrCreate(
                ['key' => 'site_logo'],
                [
                    'value' => $path,
                    'type' => 'image',
                    'group' => 'general',
                ]
            );
        }

        /*
        |--------------------------------------------------------------------------
        | حذف جميع نسخ الإعدادات المخزنة مؤقتًا
        |--------------------------------------------------------------------------
        */

        Cache::forget('site_settings');
        Cache::forget('settings');
        Cache::forget('all_settings');
        Cache::forget('social_settings');

        if (class_exists(ActivityLog::class)) {
            ActivityLog::log(
                'update',
                'settings',
                'Updated site settings'
            );
        }

        return back()->with(
            'success',
            'تم حفظ إعدادات الصحيفة بنجاح.'
        );
    }

    /**
     * حذف الشعار إذا كان ملفًا محليًا مُدارًا بواسطة النظام.
     */
    private function deleteManagedLogo(?string $path): void
    {
        if (blank($path)) {
            return;
        }

        if (filter_var($path, FILTER_VALIDATE_URL)) {
            return;
        }

        $normalizedPath = ltrim($path, '/');

        if (Storage::disk('public')->exists($normalizedPath)) {
            Storage::disk('public')->delete($normalizedPath);
        }
    }
}