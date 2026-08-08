@extends('layouts.admin')

@section('title', __('admin.settings_index'))

@section('breadcrumb')
    {{ __('admin.nav_settings') }}
@endsection

@section('content')

@php
    $allSettings = $settings->flatten(1)->keyBy('key');

    $currentLogo = $allSettings->get('site_logo')?->value;

    $currentLogoUrl = $currentLogo
        ? (
            filter_var($currentLogo, FILTER_VALIDATE_URL)
                ? $currentLogo
                : asset('storage/' . ltrim($currentLogo, '/'))
        )
        : asset('images/logo.png');

    $socials = [
        'facebook_url' => [
            'label'       => 'فيسبوك',
            'icon'        => 'fa-brands fa-facebook-f',
            'color'       => '#1877f2',
            'placeholder' => 'https://facebook.com/...',
        ],

        'instagram_url' => [
            'label'       => 'إنستغرام',
            'icon'        => 'fa-brands fa-instagram',
            'color'       => '#e4405f',
            'placeholder' => 'https://instagram.com/...',
        ],

        'youtube_url' => [
            'label'       => 'يوتيوب',
            'icon'        => 'fa-brands fa-youtube',
            'color'       => '#ff0000',
            'placeholder' => 'https://youtube.com/@...',
        ],

        'twitter_url' => [
            'label'       => 'X (تويتر)',
            'icon'        => 'fa-brands fa-x-twitter',
            'color'       => '#111111',
            'placeholder' => 'https://x.com/...',
        ],

        'linkedin_url' => [
            'label'       => 'LinkedIn',
            'icon'        => 'fa-brands fa-linkedin-in',
            'color'       => '#0a66c2',
            'placeholder' => 'https://linkedin.com/company/...',
        ],

        'tiktok_url' => [
            'label'       => 'TikTok',
            'icon'        => 'fa-brands fa-tiktok',
            'color'       => '#111111',
            'placeholder' => 'https://tiktok.com/@...',
        ],

        'telegram_url' => [
            'label'       => 'Telegram',
            'icon'        => 'fa-brands fa-telegram',
            'color'       => '#229ed9',
            'placeholder' => 'https://t.me/...',
        ],

        'whatsapp_url' => [
            'label'       => 'WhatsApp',
            'icon'        => 'fa-brands fa-whatsapp',
            'color'       => '#25d366',
            'placeholder' => 'https://wa.me/970...',
        ],
    ];

    /*
    |--------------------------------------------------------------------------
    | الحقول التي لن تظهر ضمن الإعدادات العامة
    |--------------------------------------------------------------------------
    |
    | حقول الأيقونات القديمة لا نحتاج إليها؛ لأن كلاس كل أيقونة محدد
    | تلقائيًا داخل مصفوفة $socials أعلاه.
    |
    */

    $excludedKeys = array_merge(
        [
            'site_logo',
            'social_icon',
            'facebook_icon',
            'instagram_icon',
            'youtube_icon',
            'twitter_icon',
            'x_icon',
            'linkedin_icon',
            'tiktok_icon',
            'telegram_icon',
            'whatsapp_icon',
        ],
        array_keys($socials)
    );

    $groupNames = [
        'general'  => __('admin.settings_group_general'),
        'contact'  => __('admin.settings_group_contact'),
        'social'   => __('admin.settings_group_social'),
        'display'  => __('admin.settings_group_display'),
        'features' => __('admin.settings_group_features'),
    ];
@endphp

@if ($errors->any())
    <div class="settings-alert settings-alert-error">
        <div class="settings-alert-title">
            <i class="fa-solid fa-circle-exclamation"></i>
            تعذر حفظ الإعدادات
        </div>

        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@if (session('success'))
    <div class="settings-alert settings-alert-success">
        <i class="fa-solid fa-circle-check"></i>
        {{ session('success') }}
    </div>
@endif

<form
    method="POST"
    action="{{ route('admin.settings.update') }}"
    enctype="multipart/form-data"
>
    @csrf

    <div class="settings-grid">

        {{-- شعار الصحيفة --}}
        <div class="card">
            <div class="card-header">
                <span class="card-title">
                    <i class="fa-regular fa-image"></i>
                    شعار الصحيفة
                </span>
            </div>

            <div class="card-body">
                <div class="logo-editor">
                    <div class="logo-preview-box">
                        <img
                            id="logoPreview"
                            src="{{ $currentLogoUrl }}"
                            alt="شعار الصحيفة"
                        >
                    </div>

                    <div class="logo-options">
                        <label class="form-label" for="site_logo">
                            اختر شعارًا جديدًا
                        </label>

                        <input
                            id="site_logo"
                            type="file"
                            name="site_logo"
                            class="form-control"
                            accept="image/png,image/jpeg,image/webp,image/svg+xml"
                        >

                        <small class="field-help">
                            الأنواع المتاحة: PNG أو JPG أو WEBP أو SVG،
                            والحد الأقصى 4MB.
                        </small>

                        @if ($currentLogo)
                            <label class="remove-logo-option">
                                <input
                                    type="checkbox"
                                    name="remove_site_logo"
                                    value="1"
                                >

                                <span>
                                    حذف الشعار المرفوع والعودة إلى الشعار الافتراضي
                                </span>
                            </label>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- روابط التواصل الاجتماعي --}}
        <div class="card">
            <div class="card-header">
                <span class="card-title">
                    <i class="fa-solid fa-share-nodes"></i>
                    وسائل التواصل الاجتماعي
                </span>
            </div>

            <div class="card-body">
                @foreach ($socials as $key => $social)
                    @php
                        $value = old(
                            'settings.' . $key,
                            $allSettings->get($key)?->value
                        );
                    @endphp

                    <div class="form-group">
                        <label class="form-label" for="{{ $key }}">
                            {{ $social['label'] }}
                        </label>

                        <div class="social-input-row">
                            <div
                                class="social-icon"
                                style="background-color: {{ $social['color'] }};"
                            >
                                <i class="{{ $social['icon'] }}"></i>
                            </div>

                            <input
                                id="{{ $key }}"
                                type="url"
                                name="settings[{{ $key }}]"
                                class="form-control"
                                value="{{ $value }}"
                                placeholder="{{ $social['placeholder'] }}"
                                dir="ltr"
                                autocomplete="url"
                            >
                        </div>

                        @error('settings.' . $key)
                            <small class="field-error">
                                {{ $message }}
                            </small>
                        @enderror
                    </div>
                @endforeach

                <small class="field-help social-help">
                    اترك رابط أي منصة فارغًا لإخفاء أيقونتها من الموقع.
                </small>
            </div>
        </div>

        {{-- بقية مجموعات الإعدادات --}}
        @foreach ($settings as $group => $groupSettings)
            @php
                $visibleSettings = $groupSettings->whereNotIn(
                    'key',
                    $excludedKeys
                );
            @endphp

            @if ($visibleSettings->isNotEmpty())
                <div class="card">
                    <div class="card-header">
                        <span class="card-title">
                            {{ $groupNames[$group] ?? $group }}
                        </span>
                    </div>

                    <div class="card-body">
                        @foreach ($visibleSettings as $setting)
                            <div class="form-group">
                                <label
                                    class="form-label"
                                    for="setting_{{ $setting->key }}"
                                >
                                    {{ $setting->key }}
                                </label>

                                @if ($setting->type === 'textarea')
                                    <textarea
                                        id="setting_{{ $setting->key }}"
                                        name="settings[{{ $setting->key }}]"
                                        class="form-control"
                                        rows="4"
                                    >{{ old(
                                        'settings.' . $setting->key,
                                        $setting->value
                                    ) }}</textarea>

                                @elseif ($setting->type === 'boolean')
                                    <select
                                        id="setting_{{ $setting->key }}"
                                        name="settings[{{ $setting->key }}]"
                                        class="form-control"
                                    >
                                        <option
                                            value="1"
                                            @selected(
                                                old(
                                                    'settings.' . $setting->key,
                                                    $setting->value
                                                ) == '1'
                                            )
                                        >
                                            مفعّل
                                        </option>

                                        <option
                                            value="0"
                                            @selected(
                                                old(
                                                    'settings.' . $setting->key,
                                                    $setting->value
                                                ) == '0'
                                            )
                                        >
                                            غير مفعّل
                                        </option>
                                    </select>

                                @else
                                    <input
                                        id="setting_{{ $setting->key }}"
                                        type="{{ $setting->type === 'number' ? 'number' : 'text' }}"
                                        name="settings[{{ $setting->key }}]"
                                        class="form-control"
                                        value="{{ old(
                                            'settings.' . $setting->key,
                                            $setting->value
                                        ) }}"
                                    >
                                @endif

                                @error('settings.' . $setting->key)
                                    <small class="field-error">
                                        {{ $message }}
                                    </small>
                                @enderror
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        @endforeach
    </div>

    <div class="settings-actions">
        <button type="submit" class="btn btn-primary">
            <i class="fa-solid fa-floppy-disk"></i>
            {{ __('admin.btn_save_settings') }}
        </button>
    </div>
</form>

<style>
    .settings-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 20px;
    }

    .logo-editor {
        display: flex;
        align-items: center;
        gap: 24px;
        flex-wrap: wrap;
    }

    .logo-preview-box {
        width: 220px;
        height: 130px;
        flex: 0 0 220px;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 15px;
        overflow: hidden;
        border: 1px dashed rgba(255, 255, 255, 0.2);
        border-radius: 12px;
        background: rgba(255, 255, 255, 0.03);
    }

    .logo-preview-box img {
        display: block;
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
    }

    .logo-options {
        flex: 1;
        min-width: 230px;
    }

    .remove-logo-option {
        display: flex;
        align-items: flex-start;
        gap: 9px;
        margin-top: 15px;
        cursor: pointer;
    }

    .remove-logo-option input {
        margin-top: 4px;
    }

    .social-input-row {
        display: flex;
        align-items: stretch;
        gap: 10px;
    }

    .social-input-row .form-control {
        flex: 1;
        min-width: 0;
    }

    .social-icon {
        width: 44px;
        height: 44px;
        flex: 0 0 44px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 9px;
        color: #ffffff;
        font-size: 19px;
    }

    .field-help {
        display: block;
        margin-top: 7px;
        color: #8b93a7;
        font-size: 12px;
        line-height: 1.7;
    }

    .social-help {
        margin-top: 15px;
    }

    .field-error {
        display: block;
        margin-top: 6px;
        color: #ef4444;
        font-size: 12px;
    }

    .settings-actions {
        display: flex;
        justify-content: flex-start;
        margin-top: 22px;
    }

    .settings-alert {
        padding: 14px 16px;
        margin-bottom: 20px;
        border: 1px solid transparent;
        border-radius: 10px;
        line-height: 1.8;
    }

    .settings-alert-error {
        color: #fecaca;
        border-color: rgba(239, 68, 68, 0.35);
        background: rgba(239, 68, 68, 0.1);
    }

    .settings-alert-success {
        color: #bbf7d0;
        border-color: rgba(34, 197, 94, 0.35);
        background: rgba(34, 197, 94, 0.1);
    }

    .settings-alert-title {
        font-weight: 700;
    }

    .settings-alert ul {
        margin: 8px 20px 0 0;
    }

    @media (max-width: 900px) {
        .settings-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 520px) {
        .logo-preview-box {
            width: 100%;
            flex-basis: 100%;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const logoInput = document.getElementById('site_logo');
        const logoPreview = document.getElementById('logoPreview');

        if (!logoInput || !logoPreview) {
            return;
        }

        logoInput.addEventListener('change', function (event) {
            const file = event.target.files?.[0];

            if (!file) {
                return;
            }

            if (!file.type.startsWith('image/')) {
                event.target.value = '';
                return;
            }

            const reader = new FileReader();

            reader.onload = function (readerEvent) {
                logoPreview.src = readerEvent.target.result;
            };

            reader.readAsDataURL(file);
        });
    });
</script>

@endsection