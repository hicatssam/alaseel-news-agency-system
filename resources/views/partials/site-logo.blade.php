@php
    $savedLogo = \App\Models\Setting::get('site_logo');

    $siteLogoUrl = filled($savedLogo)
        ? asset('storage/' . ltrim($savedLogo, '/'))
        : asset('images/logo.png');

    $siteName = \App\Models\Setting::get(
        'site_name',
        config('app.name')
    );
@endphp

<img
    src="{{ $siteLogoUrl }}"
    alt="{{ $siteName }}"
    class="{{ $class ?? 'site-logo' }}"
    style="{{ $style ?? '' }}"
>