@extends('layouts.app')

@section('title', $video->title)

@section('content')
@php
    /*
    |--------------------------------------------------------------------------
    | تنظيف الروابط
    |--------------------------------------------------------------------------
    */
    $cleanUrl = function ($value) {
        if (! is_string($value) || blank($value)) {
            return null;
        }

        $value = trim($value);

        // تنظيف رابط محفوظ بصيغة Markdown:
        // [https://example.com](https://example.com)
        if (preg_match(
            '/^\[[^\]]+\]\((https?:\/\/[^)]+)\)$/i',
            $value,
            $matches
        )) {
            $value = $matches[1];
        }

        // استخراج src إذا تم حفظ iframe كاملًا.
        if (preg_match(
            '/<iframe[^>]+src=["\']([^"\']+)["\']/i',
            $value,
            $matches
        )) {
            $value = $matches[1];
        }

        return trim(html_entity_decode($value));
    };

    /*
    |--------------------------------------------------------------------------
    | تجهيز روابط الصور وملفات الفيديو
    |--------------------------------------------------------------------------
    */
    $mediaUrl = function ($media) use ($cleanUrl) {
        $media = $cleanUrl($media);

        if (blank($media)) {
            return null;
        }

        $media = str_replace('\\', '/', $media);

        if (\Illuminate\Support\Str::startsWith(
            $media,
            ['http://', 'https://', '//', 'data:']
        )) {
            return $media;
        }

        $media = preg_replace(
            '#^/?(?:public/|storage/)+#',
            '',
            $media
        );

        return asset('storage/' . ltrim($media, '/'));
    };

    /*
    |--------------------------------------------------------------------------
    | تحويل روابط YouTube وVimeo إلى Embed
    |--------------------------------------------------------------------------
    */
    $makeEmbedUrl = function ($video) use ($cleanUrl) {
        $url = $cleanUrl($video->embed_url);

        if (blank($url)) {
            $url = $cleanUrl($video->video_url);
        }

        if (blank($url)) {
            return null;
        }

        // رابط YouTube Embed جاهز.
        if (preg_match(
            '#youtube(?:-nocookie)?\.com/embed/([A-Za-z0-9_-]{6,})#i',
            $url,
            $matches
        )) {
            return 'https://www.youtube.com/embed/'
                . rawurlencode($matches[1]);
        }

        // روابط youtube.com/watch?v=
        $queryString = parse_url($url, PHP_URL_QUERY);

        if ($queryString) {
            parse_str($queryString, $query);

            if (! empty($query['v'])) {
                return 'https://www.youtube.com/embed/'
                    . rawurlencode($query['v']);
            }
        }

        // روابط youtu.be وShorts وLive.
        if (preg_match(
            '#(?:youtu\.be/|youtube\.com/(?:shorts|live)/)([A-Za-z0-9_-]{6,})#i',
            $url,
            $matches
        )) {
            return 'https://www.youtube.com/embed/'
                . rawurlencode($matches[1]);
        }

        // روابط Vimeo.
        if (preg_match(
            '#(?:vimeo\.com/|player\.vimeo\.com/video/)([0-9]+)#i',
            $url,
            $matches
        )) {
            return 'https://player.vimeo.com/video/' . $matches[1];
        }

        return null;
    };

    $embedUrl = $makeEmbedUrl($video);
    $videoFileUrl = null;
    $thumbnailUrl = $mediaUrl($video->thumbnail);

    if (filled($video->video_url) && blank($embedUrl)) {
        $videoFileUrl = $mediaUrl($video->video_url);
    }

    // منع الخطأ إذا لم يرسل الكنترولر إعلانات.
    $videoAdsList = isset($videoAds)
        ? $videoAds
        : collect();
@endphp

<div class="main-content">
    <div class="container" style="max-width:1200px">

        <div style="margin-bottom:24px">
            <a
                href="{{ route('videos.index') }}"
                style="
                    display:inline-flex;
                    align-items:center;
                    gap:8px;
                    color:var(--gold);
                    text-decoration:none;
                    font-size:14px;
                "
            >
                <i class="fa-solid fa-arrow-right"></i>
                جميع الفيديوهات
            </a>
        </div>

        @if($videoAdsList->isNotEmpty())
            <div style="
                display:flex;
                flex-direction:column;
                gap:14px;
                margin-bottom:24px;
            ">
                @foreach($videoAdsList as $ad)
                    @php
                        $adImage = $mediaUrl(
                            $ad->image ?? $ad->image_url ?? null
                        );
                    @endphp

                    <div style="
                        padding:10px;
                        text-align:center;
                        background:var(--surface);
                        border:1px solid var(--border);
                        border-radius:12px;
                        overflow:hidden;
                    ">
                        @if($ad->link)
                            <a
                                href="{{ $ad->link }}"
                                target="_blank"
                                rel="noopener noreferrer"
                                style="display:block"
                            >
                        @endif

                        @if($adImage)
                            <img
                                src="{{ $adImage }}"
                                alt="{{ $ad->title }}"
                                style="
                                    display:block;
                                    width:100%;
                                    max-height:280px;
                                    object-fit:contain;
                                    border-radius:8px;
                                "
                            >
                        @else
                            <div style="padding:25px">
                                {{ $ad->title }}
                            </div>
                        @endif

                        @if($ad->link)
                            </a>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif

        <div class="video-page-grid" style="
            display:grid;
            grid-template-columns:minmax(0,2fr) minmax(280px,1fr);
            gap:28px;
            align-items:start;
        ">
            <main>
                <article style="
                    background:var(--surface);
                    border:1px solid var(--border);
                    border-radius:14px;
                    overflow:hidden;
                ">
                    <div style="
                        position:relative;
                        width:100%;
                        aspect-ratio:16/9;
                        background:#000;
                    ">
                        @if($embedUrl)
                            <iframe
                                src="{{ $embedUrl }}"
                                title="{{ $video->title }}"
                                loading="lazy"
                                referrerpolicy="strict-origin-when-cross-origin"
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                                allowfullscreen
                                style="
                                    position:absolute;
                                    inset:0;
                                    width:100%;
                                    height:100%;
                                    border:0;
                                "
                            ></iframe>
                        @elseif($videoFileUrl)
                            <video
                                controls
                                preload="metadata"
                                @if($thumbnailUrl)
                                    poster="{{ $thumbnailUrl }}"
                                @endif
                                style="
                                    position:absolute;
                                    inset:0;
                                    width:100%;
                                    height:100%;
                                    object-fit:contain;
                                    background:#000;
                                "
                            >
                                <source src="{{ $videoFileUrl }}">

                                متصفحك لا يدعم تشغيل الفيديو.
                            </video>
                        @elseif($thumbnailUrl)
                            <img
                                src="{{ $thumbnailUrl }}"
                                alt="{{ $video->title }}"
                                style="
                                    width:100%;
                                    height:100%;
                                    object-fit:cover;
                                "
                            >
                        @else
                            <div style="
                                height:100%;
                                display:flex;
                                align-items:center;
                                justify-content:center;
                                flex-direction:column;
                                gap:12px;
                                color:#aaa;
                            ">
                                <i
                                    class="fa-solid fa-video-slash"
                                    style="font-size:48px"
                                ></i>

                                <span>الفيديو غير متوفر حاليًا</span>
                            </div>
                        @endif
                    </div>

                    <div style="padding:24px">
                        @if($video->category)
                            <a
                                href="{{ route('videos.index', [
                                    'category_id' => $video->category_id
                                ]) }}"
                                style="
                                    display:inline-block;
                                    margin-bottom:12px;
                                    padding:5px 12px;
                                    background:rgba(201,168,76,.12);
                                    border:1px solid rgba(201,168,76,.35);
                                    border-radius:20px;
                                    color:var(--gold);
                                    font-size:13px;
                                    text-decoration:none;
                                "
                            >
                                {{ $video->category->name }}
                            </a>
                        @endif

                        <h1 style="
                            margin:0 0 14px;
                            color:var(--text);
                            font-size:clamp(24px,4vw,38px);
                            line-height:1.5;
                        ">
                            {{ $video->title }}
                        </h1>

                        <div style="
                            display:flex;
                            flex-wrap:wrap;
                            gap:18px;
                            margin-bottom:20px;
                            color:var(--muted);
                            font-size:14px;
                        ">
                            <span>
                                <i class="fa-regular fa-eye"></i>

                                {{ number_format($video->views ?? 0) }}
                                مشاهدة
                            </span>

                            @if($video->published_at)
                                <span>
                                    <i class="fa-regular fa-calendar"></i>

                                    {{ $video->published_at->format('Y/m/d') }}
                                </span>
                            @endif
                        </div>

                        @if($video->description)
                            <div style="
                                color:var(--text);
                                font-size:16px;
                                line-height:2;
                                white-space:pre-line;
                            ">{{ $video->description }}</div>
                        @endif
                    </div>
                </article>
            </main>

            <aside>
                <div class="sidebar-widget">
                    <div style="
                        padding:18px 20px;
                        border-bottom:1px solid var(--border);
                    ">
                        <h2 style="
                            margin:0;
                            color:var(--text);
                            font-size:18px;
                        ">
                            فيديوهات ذات صلة
                        </h2>
                    </div>

                    @forelse($related as $item)
                        @php
                            $relatedThumbnail = $mediaUrl(
                                $item->thumbnail
                            );
                        @endphp

                        <a
                            href="{{ route('videos.show', $item->slug) }}"
                            style="
                                display:grid;
                                grid-template-columns:120px minmax(0,1fr);
                                gap:12px;
                                padding:14px;
                                color:inherit;
                                text-decoration:none;
                                border-bottom:1px solid var(--border);
                            "
                        >
                            <div style="
                                position:relative;
                                aspect-ratio:16/9;
                                overflow:hidden;
                                background:#111;
                                border-radius:7px;
                            ">
                                @if($relatedThumbnail)
                                    <img
                                        src="{{ $relatedThumbnail }}"
                                        alt="{{ $item->title }}"
                                        loading="lazy"
                                        style="
                                            width:100%;
                                            height:100%;
                                            object-fit:cover;
                                        "
                                    >
                                @endif

                                <span style="
                                    position:absolute;
                                    inset:0;
                                    display:flex;
                                    align-items:center;
                                    justify-content:center;
                                    color:#fff;
                                    background:rgba(0,0,0,.2);
                                ">
                                    <i class="fa-solid fa-play"></i>
                                </span>
                            </div>

                            <div>
                                <h3 style="
                                    margin:0 0 8px;
                                    color:var(--text);
                                    font-size:14px;
                                    line-height:1.6;
                                ">
                                    {{ \Illuminate\Support\Str::limit(
                                        $item->title,
                                        65
                                    ) }}
                                </h3>

                                <span style="
                                    color:var(--muted);
                                    font-size:12px;
                                ">
                                    <i class="fa-regular fa-eye"></i>

                                    {{ number_format($item->views ?? 0) }}
                                </span>
                            </div>
                        </a>
                    @empty
                        <div style="
                            padding:30px 20px;
                            text-align:center;
                            color:var(--muted);
                        ">
                            لا توجد فيديوهات ذات صلة.
                        </div>
                    @endforelse
                </div>
            </aside>
        </div>
    </div>
</div>

<style>
    @media (max-width: 850px) {
        .video-page-grid {
            grid-template-columns: 1fr !important;
        }
    }
</style>
@endsection