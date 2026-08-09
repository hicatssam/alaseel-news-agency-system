@extends('layouts.app')

@php
    use Illuminate\Support\Facades\Route;
    use Illuminate\Support\Facades\Storage;
    use Illuminate\Support\Str;

    $currentLocale = app()->getLocale();
    $isArabic = Str::startsWith($currentLocale, 'ar');
    $pageDirection = $isArabic ? 'rtl' : 'ltr';

    /*
    |--------------------------------------------------------------------------
    | Localized value helper
    |--------------------------------------------------------------------------
    */
    $localizedValue = static function ($model, string $field, $default = null) use ($currentLocale, $isArabic) {
        if (!$model) {
            return $default;
        }

        $locale = Str::before($currentLocale, '_');

        $possibleFields = array_unique([
            $field . '_' . $currentLocale,
            $field . '_' . $locale,
            $field . '_' . ($isArabic ? 'ar' : 'en'),
            $field,
        ]);

        foreach ($possibleFields as $possibleField) {
            $value = data_get($model, $possibleField);

            if ($value !== null && trim((string) $value) !== '') {
                return $value;
            }
        }

        return $default;
    };

    /*
    |--------------------------------------------------------------------------
    | Public image URL helper
    |--------------------------------------------------------------------------
    | Supports:
    | - Spatie Media Library
    | - Full external URLs
    | - /storage/... paths
    | - Files saved on Laravel's public disk
    */
    $publicImageUrl = static function (
        $model,
        array $fields = ['image'],
        array $mediaCollections = []
    ) {
        if (!$model) {
            return null;
        }

        if (method_exists($model, 'getFirstMediaUrl')) {
            foreach ($mediaCollections as $collection) {
                try {
                    $mediaUrl = $model->getFirstMediaUrl($collection);

                    if (filled($mediaUrl)) {
                        return $mediaUrl;
                    }
                } catch (\Throwable $exception) {
                    // Continue to normal image fields.
                }
            }
        }

        foreach ($fields as $field) {
            $image = data_get($model, $field);

            if (!filled($image)) {
                continue;
            }

            $image = trim(str_replace('\\', '/', (string) $image));

            if (
                Str::startsWith($image, [
                    'http://',
                    'https://',
                    '//',
                ])
            ) {
                return $image;
            }

            if (Str::startsWith($image, '/')) {
                return url($image);
            }

            $image = Str::after($image, 'storage/app/public/');
            $image = Str::after($image, 'public/');

            if (Str::startsWith($image, 'storage/')) {
                return asset($image);
            }

            return Storage::disk('public')->url(ltrim($image, '/'));
        }

        return null;
    };

    /*
    |--------------------------------------------------------------------------
    | Safe rich HTML rendering
    |--------------------------------------------------------------------------
    | HTMLPurifier removes scripts, event handlers, unsafe iframes and
    | javascript: links while preserving safe article formatting.
    */
    $sanitizeHtml = static function ($html) {
        $html = (string) $html;

        if ($html === '') {
            return '';
        }

        if (class_exists(\HTMLPurifier::class) && class_exists(\HTMLPurifier_Config::class)) {
            $config = \HTMLPurifier_Config::createDefault();

            $config->set(
                'HTML.Allowed',
                implode(',', [
                    'p[class|style]',
                    'br',
                    'hr',
                    'h1[class|style]',
                    'h2[class|style]',
                    'h3[class|style]',
                    'h4[class|style]',
                    'h5[class|style]',
                    'h6[class|style]',
                    'strong',
                    'b',
                    'em',
                    'i',
                    'u',
                    's',
                    'strike',
                    'span[class|style]',
                    'div[class|style|dir]',
                    'blockquote[class]',
                    'ul[class|style]',
                    'ol[class|style|start]',
                    'li[class|style]',
                    'a[href|title|target|rel]',
                    'img[src|alt|title|width|height|class|style]',
                    'table[class|style]',
                    'thead',
                    'tbody',
                    'tfoot',
                    'tr',
                    'th[colspan|rowspan|scope|class|style]',
                    'td[colspan|rowspan|class|style]',
                ])
            );

            $config->set('HTML.SafeIframe', false);
            $config->set('HTML.Nofollow', true);
            $config->set('HTML.TargetBlank', true);
            $config->set('URI.DisableExternalResources', false);
            $config->set('URI.DisableResources', false);

            $config->set('URI.AllowedSchemes', [
                'http' => true,
                'https' => true,
                'mailto' => true,
                'tel' => true,
            ]);

            $config->set('CSS.AllowedProperties', [
                'text-align',
                'direction',
                'color',
                'background-color',
                'font-family',
                'font-size',
                'font-weight',
                'font-style',
                'text-decoration',
                'margin',
                'margin-left',
                'margin-right',
                'padding',
                'width',
                'height',
                'max-width',
                'float',
                'clear',
                'border',
                'border-width',
                'border-style',
                'border-color',
            ]);

            return (new \HTMLPurifier($config))->purify($html);
        }

        return nl2br(e(strip_tags($html)));
    };

    /*
    |--------------------------------------------------------------------------
    | About page content
    |--------------------------------------------------------------------------
    */
    $aboutTitle = $localizedValue(
        $aboutPage ?? null,
        'title',
        $isArabic ? 'من نحن' : 'About Us'
    );

    $aboutSubtitle = $localizedValue(
        $aboutPage ?? null,
        'subtitle',
        $isArabic
            ? 'تعرف على رؤيتنا ورسالتنا والفريق الذي يقف خلف الأصيل'
            : 'Discover our vision, mission and the team behind Alaseel'
    );

    $aboutContent = $localizedValue(
        $aboutPage ?? null,
        'content',
        $localizedValue($aboutPage ?? null, 'description', '')
    );

    $vision = $localizedValue($aboutPage ?? null, 'vision', '');
    $mission = $localizedValue($aboutPage ?? null, 'mission', '');
    $values = $localizedValue($aboutPage ?? null, 'values', '');

    $aboutImage = $publicImageUrl(
        $aboutPage ?? null,
        [
            'image',
            'image_path',
            'about_image',
            'featured_image',
            'cover_image',
        ],
        [
            'about-image',
            'about_image',
            'image',
            'featured-image',
        ]
    );

    $heroImage = $publicImageUrl(
        $aboutPage ?? null,
        [
            'hero_image',
            'hero_image_path',
            'banner_image',
            'cover_image',
        ],
        [
            'hero-image',
            'hero_image',
            'banner',
            'cover',
        ]
    );

    $members = collect($teamMembers ?? []);

    $homeUrl = Route::has('home') ? route('home') : url('/');
@endphp

@section('title', $aboutTitle)

@section('content')
    <main
        class="alaseel-about-page"
        dir="{{ $pageDirection }}"
        aria-labelledby="about-page-title"
    >
        <section
            class="about-hero"
            @if ($heroImage)
                style="background-image:
                    linear-gradient(
                        135deg,
                        rgba(18, 18, 18, 0.92),
                        rgba(18, 18, 18, 0.68)
                    ),
                    url('{{ $heroImage }}');"
            @endif
        >
            <div class="about-container">
                <nav class="about-breadcrumb" aria-label="{{ $isArabic ? 'مسار التنقل' : 'Breadcrumb' }}">
                    <a href="{{ $homeUrl }}">
                        {{ $isArabic ? 'الرئيسية' : 'Home' }}
                    </a>

                    <span aria-hidden="true">
                        {{ $isArabic ? '‹' : '›' }}
                    </span>

                    <span aria-current="page">{{ $aboutTitle }}</span>
                </nav>

                <div class="about-hero-content">
                    <span class="about-section-label">
                        {{ $isArabic ? 'الأصيل' : 'Alaseel' }}
                    </span>

                    <h1 id="about-page-title">{{ $aboutTitle }}</h1>

                    @if (filled($aboutSubtitle))
                        <p>{{ $aboutSubtitle }}</p>
                    @endif
                </div>
            </div>
        </section>

        <section class="about-introduction">
            <div class="about-container">
                <div class="about-introduction-grid {{ !$aboutImage ? 'without-image' : '' }}">
                    <article class="about-text-column">
                        <header class="about-section-header">
                            <span class="about-section-label">
                                {{ $isArabic ? 'نبذة عنا' : 'Who we are' }}
                            </span>

                            <h2>
                                {{ $localizedValue(
                                    $aboutPage ?? null,
                                    'section_title',
                                    $aboutTitle
                                ) }}
                            </h2>

                            <span class="about-title-line" aria-hidden="true"></span>
                        </header>

                        @if (filled($aboutContent))
                            <div class="about-rich-content">
                                {!! $sanitizeHtml($aboutContent) !!}
                            </div>
                        @else
                            <p class="about-empty-content">
                                {{ $isArabic
                                    ? 'سيتم إضافة محتوى صفحة من نحن قريبًا.'
                                    : 'The About Us content will be added soon.' }}
                            </p>
                        @endif
                    </article>

                    @if ($aboutImage)
                        <figure class="about-featured-image">
                            <span class="about-image-decoration" aria-hidden="true"></span>

                            <img
                                src="{{ $aboutImage }}"
                                alt="{{ $aboutTitle }}"
                                loading="lazy"
                                decoding="async"
                            >
                        </figure>
                    @endif
                </div>
            </div>
        </section>

        @if (filled($vision) || filled($mission) || filled($values))
            <section class="about-principles">
                <div class="about-container">
                    <div class="about-principles-grid">
                        @if (filled($vision))
                            <article class="about-principle-card">
                                <span class="principle-number" aria-hidden="true">01</span>

                                <div class="principle-icon" aria-hidden="true">
                                    <svg viewBox="0 0 24 24">
                                        <path d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6S2 12 2 12Z"/>
                                        <circle cx="12" cy="12" r="3"/>
                                    </svg>
                                </div>

                                <h2>{{ $isArabic ? 'رؤيتنا' : 'Our Vision' }}</h2>

                                <div class="about-rich-content">
                                    {!! $sanitizeHtml($vision) !!}
                                </div>
                            </article>
                        @endif

                        @if (filled($mission))
                            <article class="about-principle-card">
                                <span class="principle-number" aria-hidden="true">02</span>

                                <div class="principle-icon" aria-hidden="true">
                                    <svg viewBox="0 0 24 24">
                                        <circle cx="12" cy="12" r="9"/>
                                        <circle cx="12" cy="12" r="5"/>
                                        <circle cx="12" cy="12" r="1.5"/>
                                    </svg>
                                </div>

                                <h2>{{ $isArabic ? 'رسالتنا' : 'Our Mission' }}</h2>

                                <div class="about-rich-content">
                                    {!! $sanitizeHtml($mission) !!}
                                </div>
                            </article>
                        @endif

                        @if (filled($values))
                            <article class="about-principle-card">
                                <span class="principle-number" aria-hidden="true">03</span>

                                <div class="principle-icon" aria-hidden="true">
                                    <svg viewBox="0 0 24 24">
                                        <path d="M12 21s-8-4.6-8-11a4.5 4.5 0 0 1 8-3 4.5 4.5 0 0 1 8 3c0 6.4-8 11-8 11Z"/>
                                    </svg>
                                </div>

                                <h2>{{ $isArabic ? 'قيمنا' : 'Our Values' }}</h2>

                                <div class="about-rich-content">
                                    {!! $sanitizeHtml($values) !!}
                                </div>
                            </article>
                        @endif
                    </div>
                </div>
            </section>
        @endif

        @if ($members->isNotEmpty())
            <section class="about-team" aria-labelledby="team-section-title">
                <div class="about-container">
                    <header class="about-team-header">
                        <span class="about-section-label">
                            {{ $isArabic ? 'فريق العمل' : 'Our Team' }}
                        </span>

                        <h2 id="team-section-title">
                            {{ $isArabic
                                ? 'تعرف على فريق الأصيل'
                                : 'Meet the Alaseel team' }}
                        </h2>

                        <span class="about-title-line" aria-hidden="true"></span>

                        <p>
                            {{ $isArabic
                                ? 'فريق مهني يعمل لتقديم محتوى إخباري موثوق ومتميز.'
                                : 'A professional team dedicated to delivering reliable and distinguished news content.' }}
                        </p>
                    </header>

                    <div class="about-team-grid">
                        @foreach ($members as $member)
                            @php
                                $memberName = $localizedValue(
                                    $member,
                                    'name',
                                    $isArabic ? 'عضو الفريق' : 'Team member'
                                );

                                $memberPosition = $localizedValue(
                                    $member,
                                    'job_title',
                                    $localizedValue(
                                        $member,
                                        'position',
                                        $localizedValue($member, 'title', '')
                                    )
                                );

                                $memberImage = $publicImageUrl(
                                    $member,
                                    [
                                        'image',
                                        'image_path',
                                        'photo',
                                        'photo_path',
                                        'avatar',
                                        'avatar_path',
                                    ],
                                    [
                                        'team-member-image',
                                        'team_member_image',
                                        'photo',
                                        'avatar',
                                        'image',
                                    ]
                                );

                                $memberInitial = Str::upper(
                                    Str::substr(trim((string) $memberName), 0, 1)
                                );
                            @endphp

                            <article class="about-team-card">
                                <div class="team-photo-wrapper">
                                    <span class="team-photo-border" aria-hidden="true"></span>

                                    @if ($memberImage)
                                        <img
                                            class="team-photo"
                                            src="{{ $memberImage }}"
                                            alt="{{ $memberName }}"
                                            loading="lazy"
                                            decoding="async"
                                        >
                                    @else
                                        <div
                                            class="team-photo team-photo-placeholder"
                                            aria-label="{{ $memberName }}"
                                        >
                                            {{ $memberInitial }}
                                        </div>
                                    @endif
                                </div>

                                <div class="team-card-content">
                                    <h3>{{ $memberName }}</h3>

                                    @if (filled($memberPosition))
                                        <p>{{ $memberPosition }}</p>
                                    @endif
                                </div>
                            </article>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif
    </main>

    <style>
        .alaseel-about-page {
            --about-primary:
                var(
                    --primary-color,
                    var(
                        --brand-primary,
                        var(--bs-primary, #b68b3c)
                    )
                );
            --about-primary-dark: #8c6728;
            --about-dark:
                var(
                    --secondary-color,
                    var(--brand-dark, #171717)
                );
            --about-text: #303030;
            --about-muted: #747474;
            --about-border: #e9e2d6;
            --about-background: #f8f6f2;
            --about-white: #ffffff;
            color: var(--about-text);
            background: var(--about-white);
            font-family: inherit;
            line-height: 1.9;
        }

        .about-container {
            width: min(1180px, calc(100% - 32px));
            margin-inline: auto;
        }

        .about-hero {
            position: relative;
            display: flex;
            min-height: 360px;
            align-items: center;
            overflow: hidden;
            color: var(--about-white);
            background-color: var(--about-dark);
            background-position: center;
            background-repeat: no-repeat;
            background-size: cover;
            isolation: isolate;
        }

        .about-hero::before {
            position: absolute;
            z-index: -1;
            inset-inline-start: -110px;
            bottom: -170px;
            width: 390px;
            height: 390px;
            border: 1px solid rgba(182, 139, 60, 0.28);
            border-radius: 50%;
            box-shadow:
                0 0 0 55px rgba(182, 139, 60, 0.05),
                0 0 0 110px rgba(182, 139, 60, 0.03);
            content: "";
        }

        .about-hero::after {
            position: absolute;
            z-index: -1;
            inset-inline-end: 5%;
            top: 20%;
            width: 90px;
            height: 3px;
            background: var(--about-primary);
            content: "";
            opacity: 0.8;
        }

        .about-breadcrumb {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 34px;
            color: rgba(255, 255, 255, 0.72);
            font-size: 0.9rem;
        }

        .about-breadcrumb a {
            color: inherit;
            text-decoration: none;
            transition: color 0.2s ease;
        }

        .about-breadcrumb a:hover {
            color: var(--about-primary);
        }

        .about-hero-content {
            max-width: 780px;
        }

        .about-section-label {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            color: var(--about-primary);
            font-size: 0.84rem;
            font-weight: 800;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .about-section-label::before {
            width: 28px;
            height: 2px;
            background: currentColor;
            content: "";
        }

        .about-hero h1 {
            margin: 12px 0;
            color: var(--about-white);
            font-size: clamp(2.35rem, 6vw, 4.7rem);
            font-weight: 900;
            line-height: 1.2;
        }

        .about-hero p {
            max-width: 690px;
            margin: 0;
            color: rgba(255, 255, 255, 0.82);
            font-size: clamp(1rem, 2vw, 1.22rem);
        }

        .about-introduction {
            padding: 100px 0;
        }

        .about-introduction-grid {
            display: grid;
            grid-template-columns: minmax(0, 1.08fr) minmax(340px, 0.92fr);
            align-items: center;
            gap: clamp(45px, 7vw, 90px);
        }

        .about-introduction-grid.without-image {
            grid-template-columns: minmax(0, 900px);
            justify-content: center;
        }

        .about-section-header h2,
        .about-team-header h2 {
            margin: 10px 0 12px;
            color: var(--about-dark);
            font-size: clamp(1.85rem, 4vw, 3rem);
            font-weight: 900;
            line-height: 1.35;
        }

        .about-title-line {
            display: block;
            width: 74px;
            height: 4px;
            border-radius: 999px;
            background: var(--about-primary);
        }

        .about-rich-content {
            margin-top: 28px;
            color: var(--about-text);
            font-size: 1.06rem;
            line-height: 2;
            overflow-wrap: anywhere;
        }

        .about-rich-content > :first-child {
            margin-top: 0;
        }

        .about-rich-content > :last-child {
            margin-bottom: 0;
        }

        .about-rich-content h1,
        .about-rich-content h2,
        .about-rich-content h3,
        .about-rich-content h4,
        .about-rich-content h5,
        .about-rich-content h6 {
            margin-top: 1.5em;
            margin-bottom: 0.65em;
            color: var(--about-dark);
            font-weight: 800;
            line-height: 1.45;
        }

        .about-rich-content a {
            color: var(--about-primary-dark);
            font-weight: 700;
            text-decoration: underline;
            text-underline-offset: 3px;
        }

        .about-rich-content img,
        .about-rich-content figure,
        .about-rich-content video {
            max-width: 100%;
            height: auto;
        }

        .about-rich-content figure {
            margin-inline: 0;
        }

        .about-rich-content figcaption {
            margin-top: 8px;
            color: var(--about-muted);
            font-size: 0.88rem;
            text-align: center;
        }

        .about-rich-content blockquote {
            margin: 25px 0;
            padding: 16px 22px;
            border-inline-start: 4px solid var(--about-primary);
            color: #565656;
            background: var(--about-background);
        }

        .about-rich-content table {
            display: block;
            width: 100%;
            max-width: 100%;
            border-collapse: collapse;
            overflow-x: auto;
        }

        .about-rich-content th,
        .about-rich-content td {
            min-width: 130px;
            padding: 10px 14px;
            border: 1px solid var(--about-border);
        }

        .about-featured-image {
            position: relative;
            margin: 0;
            padding: 18px;
        }

        .about-featured-image img {
            position: relative;
            z-index: 2;
            display: block;
            width: 100%;
            min-height: 480px;
            max-height: 620px;
            border-radius: 6px;
            object-fit: cover;
            box-shadow: 0 24px 60px rgba(28, 24, 17, 0.18);
        }

        .about-image-decoration {
            position: absolute;
            z-index: 1;
            inset-inline-end: 0;
            bottom: 0;
            width: 70%;
            height: 70%;
            border: 3px solid var(--about-primary);
            border-radius: 6px;
        }

        .about-featured-image::before {
            position: absolute;
            z-index: 3;
            inset-inline-start: 0;
            top: 0;
            width: 90px;
            height: 90px;
            border-top: 4px solid var(--about-primary);
            border-inline-start: 4px solid var(--about-primary);
            content: "";
        }

        .about-empty-content {
            margin-top: 28px;
            color: var(--about-muted);
        }

        .about-principles {
            padding: 90px 0;
            background:
                linear-gradient(
                    135deg,
                    var(--about-dark),
                    #26231f
                );
        }

        .about-principles-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 24px;
        }

        .about-principle-card {
            position: relative;
            min-height: 310px;
            padding: 38px 32px;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            color: rgba(255, 255, 255, 0.78);
            background: rgba(255, 255, 255, 0.045);
            transition:
                transform 0.25s ease,
                border-color 0.25s ease,
                background 0.25s ease;
        }

        .about-principle-card:hover {
            border-color: rgba(182, 139, 60, 0.7);
            background: rgba(255, 255, 255, 0.075);
            transform: translateY(-6px);
        }

        .principle-number {
            position: absolute;
            inset-inline-end: 20px;
            top: 8px;
            color: rgba(255, 255, 255, 0.05);
            font-size: 5.5rem;
            font-weight: 900;
            line-height: 1;
        }

        .principle-icon {
            display: grid;
            width: 58px;
            height: 58px;
            place-items: center;
            margin-bottom: 24px;
            border: 1px solid rgba(182, 139, 60, 0.55);
            border-radius: 50%;
            color: var(--about-primary);
        }

        .principle-icon svg {
            width: 27px;
            height: 27px;
            fill: none;
            stroke: currentColor;
            stroke-linecap: round;
            stroke-linejoin: round;
            stroke-width: 1.7;
        }

        .about-principle-card h2 {
            position: relative;
            margin: 0;
            color: var(--about-white);
            font-size: 1.45rem;
            font-weight: 800;
        }

        .about-principle-card .about-rich-content {
            position: relative;
            margin-top: 14px;
            color: rgba(255, 255, 255, 0.72);
            font-size: 0.98rem;
            line-height: 1.9;
        }

        .about-principle-card .about-rich-content h1,
        .about-principle-card .about-rich-content h2,
        .about-principle-card .about-rich-content h3 {
            color: var(--about-white);
        }

        .about-team {
            padding: 100px 0 110px;
            background: var(--about-background);
        }

        .about-team-header {
            max-width: 700px;
            margin: 0 auto 60px;
            text-align: center;
        }

        .about-team-header .about-section-label {
            justify-content: center;
        }

        .about-team-header .about-title-line {
            margin-inline: auto;
        }

        .about-team-header p {
            margin: 20px auto 0;
            color: var(--about-muted);
            font-size: 1.03rem;
        }

       .about-team-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 260px));
    justify-content: center;
    align-items: stretch;
    gap: 28px;
}

.about-team-card {
    width: 100%;
    padding: 32px 20px 27px;
    border: 1px solid var(--about-border);
    border-radius: 8px;
    background: var(--about-white);
    text-align: center;
    box-shadow: 0 12px 35px rgba(32, 27, 19, 0.06);
    transition:
        transform 0.25s ease,
        box-shadow 0.25s ease,
        border-color 0.25s ease;
}

        .about-team-card:hover {
            border-color: rgba(182, 139, 60, 0.55);
            box-shadow: 0 20px 45px rgba(32, 27, 19, 0.12);
            transform: translateY(-8px);
        }

        .team-photo-wrapper {
            position: relative;
            width: 180px;
            height: 180px;
            margin: 0 auto 24px;
        }

        .team-photo-border {
            position: absolute;
            inset: -7px;
            border: 2px solid var(--about-primary);
            border-radius: 50%;
            opacity: 0.45;
            transition:
                inset 0.25s ease,
                opacity 0.25s ease;
        }

        .about-team-card:hover .team-photo-border {
            inset: -11px;
            opacity: 1;
        }

        .team-photo {
            position: relative;
            display: block;
            width: 180px;
            height: 180px;
            border: 5px solid var(--about-white);
            border-radius: 50%;
            object-fit: cover;
            box-shadow: 0 10px 30px rgba(28, 24, 17, 0.18);
        }

        .team-photo-placeholder {
            display: grid;
            place-items: center;
            color: var(--about-white);
            background:
                linear-gradient(
                    135deg,
                    var(--about-primary),
                    var(--about-primary-dark)
                );
            font-size: 3.2rem;
            font-weight: 900;
        }

        .team-card-content h3 {
            margin: 0;
            color: var(--about-dark);
            font-size: 1.22rem;
            font-weight: 900;
            line-height: 1.5;
        }

        .team-card-content p {
            margin: 7px 0 0;
            color: var(--about-primary-dark);
            font-size: 0.92rem;
            font-weight: 700;
        }

        @media (max-width: 1100px) {
            .about-team-grid {
        grid-template-columns: minmax(0, 360px);
        justify-content: center;
    }
        }

        @media (max-width: 900px) {
            .about-introduction-grid {
                grid-template-columns: 1fr;
            }

            .about-featured-image {
                width: min(100%, 650px);
                margin-inline: auto;
            }

            .about-principles-grid {
                grid-template-columns: 1fr;
            }

            .about-principle-card {
                min-height: 0;
            }

            .about-team-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 600px) {
            .about-container {
                width: min(100% - 24px, 1180px);
            }

            .about-hero {
                min-height: 320px;
            }

            .about-introduction,
            .about-team {
                padding: 70px 0;
            }

            .about-featured-image {
                padding: 10px;
            }

            .about-featured-image img {
                min-height: 330px;
            }

            .about-principles {
                padding: 65px 0;
            }

            .about-principle-card {
                padding: 30px 24px;
            }

            .about-team-grid {
                grid-template-columns: 1fr;
            }

            .about-team-card {
                width: min(100%, 360px);
                margin-inline: auto;
            }

            .team-photo-wrapper,
            .team-photo {
                width: 160px;
                height: 160px;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            .about-team-card,
            .team-photo-border,
            .about-principle-card {
                transition: none;
            }
        }
    </style>
@endsection