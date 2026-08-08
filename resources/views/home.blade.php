@extends('layouts.app')
@section('title',__('messages.nav_home'))
@section('content')
@php
  $isRtl = app()->getLocale() === 'ar';

  /*
   * استخدم الإعلانات القادمة من الـ Controller أولاً. وإذا لم تُرسل المتغيرات
   * أو وصلت فارغة، اجلب الإعلانات الصالحة مباشرة حتى لا يختفي الإعلان بالكامل.
   */
  $loadAds = static function (string $position) {
      return \App\Models\Advertisement::query()
          ->where('position', $position)
          ->where('status', true)
          ->where(function ($query) {
              $query->whereNull('starts_at')
                  ->orWhere('starts_at', '<=', now());
          })
          ->where(function ($query) {
              $query->whereNull('ends_at')
                  ->orWhere('ends_at', '>=', now());
          })
          ->latest()
          ->get();
  };

  $homepageAds = isset($homepageAds) && $homepageAds->isNotEmpty()
      ? $homepageAds
      : $loadAds('homepage');

  $sidebarAds = isset($sidebarAds) && $sidebarAds->isNotEmpty()
      ? $sidebarAds
      : $loadAds('sidebar');


  $adMediaUrl = static function (?string $media): ?string {
      if (blank($media)) {
          return null;
      }

      $media = trim($media);

      if (
          str_starts_with($media, 'http://') ||
          str_starts_with($media, 'https://') ||
          str_starts_with($media, '//')
      ) {
          return $media;
      }

      $media = ltrim($media, '/');

      // بعض السجلات القديمة قد تحتوي المسار الكامل داخل storage/app/public.
      if (str_starts_with($media, 'storage/app/public/')) {
          $media = substr($media, strlen('storage/app/public/'));
      }

      // وبعضها قد يكون محفوظًا بصيغة public/ads/image.jpg.
      if (str_starts_with($media, 'public/')) {
          $media = substr($media, strlen('public/'));
      }

      if (str_starts_with($media, 'storage/')) {
          return asset($media);
      }

      return asset('storage/' . $media);
  };

  // يدعم أسماء الحقول الحالية والقديمة للصورة أو الفيديو.
  $adMediaValue = static function ($ad): ?string {
      if (filled($ad->media_path ?? null)) {
          return $ad->media_path;
      }

      if (filled($ad->image ?? null)) {
          return $ad->image;
      }

      if (filled($ad->image_path ?? null)) {
          return $ad->image_path;
      }

      return null;
  };

  $adIsVideo = static function ($ad, ?string $media): bool {
      if (strtolower((string) ($ad->type ?? '')) === 'video') {
          return true;
      }

      $path = strtolower((string) parse_url((string) $media, PHP_URL_PATH));

      return in_array(pathinfo($path, PATHINFO_EXTENSION), [
          'mp4', 'webm', 'ogg', 'mov', 'm4v',
      ], true);
  };

  $adVideoMime = static function (?string $media): string {
      $path = strtolower((string) parse_url((string) $media, PHP_URL_PATH));

      return match (pathinfo($path, PATHINFO_EXTENSION)) {
          'webm' => 'video/webm',
          'ogg', 'ogv' => 'video/ogg',
          'mov' => 'video/quicktime',
          default => 'video/mp4',
      };
  };
@endphp

<style>
  .home-page { padding-block: 30px 48px; }
  .home-section { margin-bottom: 34px; }
  .home-layout { display: grid; grid-template-columns: minmax(0, 1fr) 310px; gap: 28px; align-items: start; }
  .home-sidebar { display: flex; flex-direction: column; gap: 18px; min-width: 0; }

  .home-ad-list { display: grid; gap: 12px; }
  .home-ad {
    position: relative;
    overflow: hidden;
    min-height: 112px;
    border: 1px solid var(--border);
    border-radius: 14px;
    background: var(--surface2);
    box-shadow: 0 12px 32px rgba(0,0,0,.16);
  }
  .ad-animate {
    opacity: 0;
    transform: translateY(22px) scale(.985);
    transition:
      opacity .65s cubic-bezier(.22,1,.36,1),
      transform .65s cubic-bezier(.22,1,.36,1),
      border-color .3s ease,
      box-shadow .3s ease;
    transition-delay: var(--ad-delay, 0ms);
    will-change: opacity, transform;
  }
  .ad-animate.is-visible {
    opacity: 1;
    transform: translate3d(0, 0, 0) scale(1);
    animation: ad-horizontal-float 3.2s ease-in-out
      calc(.7s + var(--ad-delay, 0ms)) infinite alternate;
  }
  .ad-animate::after {
    content: '';
    position: absolute;
    inset: 0;
    z-index: 2;
    pointer-events: none;
    background: linear-gradient(105deg, transparent 35%, rgba(255,255,255,.16) 50%, transparent 65%);
    transform: translateX(-130%);
  }
  [dir="rtl"] .ad-animate::after { transform: translateX(130%); }
  .ad-animate.is-visible::after { animation: ad-shine 1.05s .35s ease-out both; }
  .ad-animate > a { position: relative; z-index: 1; }
  .ad-animate img,
  .ad-animate video { transition: transform .55s cubic-bezier(.22,1,.36,1), filter .4s ease; }
  .ad-animate:hover {
    animation-play-state: paused;
    border-color: rgba(200,154,43,.55);
    box-shadow: 0 18px 42px rgba(0,0,0,.24), 0 0 0 1px rgba(200,154,43,.12);
  }
  .ad-animate:hover img,
  .ad-animate:hover video { transform: scale(1.035); filter: saturate(1.08) contrast(1.03); }
  @keyframes ad-shine {
    from { transform: translateX(-130%); }
    to { transform: translateX(130%); }
  }
  [dir="rtl"] .ad-animate.is-visible::after { animation-name: ad-shine-rtl; }
  @keyframes ad-shine-rtl {
    from { transform: translateX(130%); }
    to { transform: translateX(-130%); }
  }
  @keyframes ad-horizontal-float {
    0%   { transform: translate3d(-14px, 0, 0) scale(1); }
    100% { transform: translate3d(14px, 0, 0) scale(1); }
  }
  .sidebar-ad.is-visible {
    animation-name: sidebar-ad-horizontal-float;
    animation-duration: 2.8s;
  }
  @keyframes sidebar-ad-horizontal-float {
    0%   { transform: translate3d(-8px, 0, 0) scale(1); }
    100% { transform: translate3d(8px, 0, 0) scale(1); }
  }
  .home-ad > a { display: block; color: inherit; }
  .home-ad-media { display: block; width: 100%; height: clamp(112px, 12vw, 170px); object-fit: cover; }
  .home-ad-fallback {
    min-height: 112px;
    padding: 24px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    text-align: center;
    background: linear-gradient(135deg, rgba(200,154,43,.15), rgba(200,154,43,.035));
  }
  .home-ad-label { margin-bottom: 5px; color: rgba(255,255,255,.38); font-size: 10px; letter-spacing: .8px; }
  .home-ad-title { color: rgba(255,255,255,.82); font-size: 14px; font-weight: 800; }

  .sidebar-ad { position: relative; overflow: hidden; border-radius: 13px; border: 1px solid var(--border); background: var(--surface2); box-shadow: 0 10px 28px rgba(0,0,0,.14); }
  .sidebar-ad > a { display: block; color: inherit; }
  .sidebar-ad-media { display: block; width: 100%; aspect-ratio: 4 / 3; object-fit: cover; }
  .sidebar-ad .home-ad-fallback { min-height: 150px; }

  /* Animated dot-matrix world map — replaces the plain black placeholder backgrounds */
  .world-map-bg {
    position: relative;
    background-color: #0c0c0d;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='640' height='320' viewBox='0 0 640 320'%3E%3Cg fill='%23C89A2B' fill-opacity='.5'%3E%3Ccircle cx='92' cy='72' r='1.8'/%3E%3Ccircle cx='98' cy='62' r='1.7'/%3E%3Ccircle cx='106' cy='142' r='1.4'/%3E%3Ccircle cx='78' cy='121' r='2.0'/%3E%3Ccircle cx='126' cy='97' r='2.0'/%3E%3Ccircle cx='87' cy='71' r='1.4'/%3E%3Ccircle cx='73' cy='116' r='1.7'/%3E%3Ccircle cx='99' cy='113' r='1.3'/%3E%3Ccircle cx='56' cy='77' r='1.8'/%3E%3Ccircle cx='106' cy='89' r='1.7'/%3E%3Ccircle cx='110' cy='87' r='1.9'/%3E%3Ccircle cx='143' cy='81' r='1.7'/%3E%3Ccircle cx='119' cy='147' r='1.8'/%3E%3Ccircle cx='64' cy='99' r='1.8'/%3E%3Ccircle cx='69' cy='107' r='1.3'/%3E%3Ccircle cx='139' cy='136' r='1.7'/%3E%3Ccircle cx='167' cy='89' r='1.8'/%3E%3Ccircle cx='129' cy='116' r='1.6'/%3E%3Ccircle cx='112' cy='125' r='1.3'/%3E%3Ccircle cx='143' cy='123' r='2.0'/%3E%3Ccircle cx='160' cy='86' r='1.6'/%3E%3Ccircle cx='111' cy='73' r='1.4'/%3E%3Ccircle cx='66' cy='82' r='1.6'/%3E%3Ccircle cx='109' cy='113' r='1.9'/%3E%3Ccircle cx='159' cy='146' r='1.5'/%3E%3Ccircle cx='104' cy='93' r='1.9'/%3E%3Ccircle cx='72' cy='80' r='1.5'/%3E%3Ccircle cx='114' cy='117' r='1.5'/%3E%3Ccircle cx='98' cy='115' r='2.0'/%3E%3Ccircle cx='142' cy='110' r='1.7'/%3E%3Ccircle cx='140' cy='62' r='1.9'/%3E%3Ccircle cx='154' cy='147' r='1.9'/%3E%3Ccircle cx='101' cy='97' r='1.4'/%3E%3Ccircle cx='134' cy='62' r='1.3'/%3E%3Ccircle cx='160' cy='177' r='1.5'/%3E%3Ccircle cx='156' cy='171' r='1.6'/%3E%3Ccircle cx='182' cy='175' r='1.5'/%3E%3Ccircle cx='167' cy='198' r='1.4'/%3E%3Ccircle cx='174' cy='210' r='1.4'/%3E%3Ccircle cx='154' cy='196' r='1.5'/%3E%3Ccircle cx='194' cy='177' r='1.3'/%3E%3Ccircle cx='201' cy='215' r='1.4'/%3E%3Ccircle cx='178' cy='163' r='1.7'/%3E%3Ccircle cx='187' cy='187' r='1.6'/%3E%3Ccircle cx='157' cy='240' r='1.7'/%3E%3Ccircle cx='192' cy='194' r='1.5'/%3E%3Ccircle cx='196' cy='244' r='1.9'/%3E%3Ccircle cx='189' cy='184' r='1.7'/%3E%3Ccircle cx='150' cy='189' r='1.5'/%3E%3Ccircle cx='173' cy='257' r='2.0'/%3E%3Ccircle cx='201' cy='198' r='1.5'/%3E%3Ccircle cx='161' cy='180' r='1.4'/%3E%3Ccircle cx='183' cy='254' r='1.9'/%3E%3Ccircle cx='175' cy='228' r='1.9'/%3E%3Ccircle cx='190' cy='210' r='1.4'/%3E%3Ccircle cx='192' cy='195' r='1.9'/%3E%3Ccircle cx='202' cy='201' r='1.6'/%3E%3Ccircle cx='158' cy='173' r='1.4'/%3E%3Ccircle cx='168' cy='217' r='1.4'/%3E%3Ccircle cx='184' cy='215' r='2.0'/%3E%3Ccircle cx='334' cy='97' r='1.9'/%3E%3Ccircle cx='321' cy='62' r='1.5'/%3E%3Ccircle cx='322' cy='81' r='1.5'/%3E%3Ccircle cx='333' cy='55' r='1.9'/%3E%3Ccircle cx='329' cy='74' r='1.7'/%3E%3Ccircle cx='362' cy='72' r='1.9'/%3E%3Ccircle cx='338' cy='78' r='1.7'/%3E%3Ccircle cx='356' cy='58' r='1.6'/%3E%3Ccircle cx='352' cy='79' r='1.5'/%3E%3Ccircle cx='339' cy='79' r='1.8'/%3E%3Ccircle cx='314' cy='79' r='1.5'/%3E%3Ccircle cx='325' cy='91' r='1.7'/%3E%3Ccircle cx='342' cy='91' r='1.9'/%3E%3Ccircle cx='335' cy='82' r='1.7'/%3E%3Ccircle cx='339' cy='87' r='1.6'/%3E%3Ccircle cx='340' cy='75' r='2.0'/%3E%3Ccircle cx='350' cy='97' r='2.0'/%3E%3Ccircle cx='324' cy='79' r='2.0'/%3E%3Ccircle cx='368' cy='123' r='1.4'/%3E%3Ccircle cx='340' cy='114' r='1.5'/%3E%3Ccircle cx='356' cy='123' r='1.9'/%3E%3Ccircle cx='378' cy='134' r='2.0'/%3E%3Ccircle cx='337' cy='170' r='2.0'/%3E%3Ccircle cx='368' cy='126' r='1.6'/%3E%3Ccircle cx='345' cy='150' r='1.4'/%3E%3Ccircle cx='331' cy='202' r='1.3'/%3E%3Ccircle cx='348' cy='164' r='1.3'/%3E%3Ccircle cx='332' cy='189' r='1.7'/%3E%3Ccircle cx='316' cy='140' r='1.3'/%3E%3Ccircle cx='364' cy='141' r='1.4'/%3E%3Ccircle cx='338' cy='228' r='1.9'/%3E%3Ccircle cx='327' cy='124' r='1.9'/%3E%3Ccircle cx='349' cy='199' r='1.4'/%3E%3Ccircle cx='339' cy='114' r='2.0'/%3E%3Ccircle cx='354' cy='213' r='1.4'/%3E%3Ccircle cx='370' cy='166' r='1.5'/%3E%3Ccircle cx='348' cy='230' r='1.5'/%3E%3Ccircle cx='317' cy='176' r='1.5'/%3E%3Ccircle cx='316' cy='126' r='1.3'/%3E%3Ccircle cx='323' cy='146' r='1.5'/%3E%3Ccircle cx='363' cy='143' r='1.7'/%3E%3Ccircle cx='321' cy='151' r='1.3'/%3E%3Ccircle cx='361' cy='179' r='1.4'/%3E%3Ccircle cx='342' cy='231' r='1.4'/%3E%3Ccircle cx='367' cy='163' r='1.6'/%3E%3Ccircle cx='368' cy='157' r='1.7'/%3E%3Ccircle cx='333' cy='217' r='1.8'/%3E%3Ccircle cx='354' cy='159' r='1.5'/%3E%3Ccircle cx='312' cy='122' r='1.3'/%3E%3Ccircle cx='361' cy='139' r='1.4'/%3E%3Ccircle cx='371' cy='195' r='1.5'/%3E%3Ccircle cx='325' cy='144' r='1.6'/%3E%3Ccircle cx='397' cy='97' r='1.5'/%3E%3Ccircle cx='469' cy='71' r='2.0'/%3E%3Ccircle cx='425' cy='86' r='1.3'/%3E%3Ccircle cx='438' cy='101' r='1.7'/%3E%3Ccircle cx='405' cy='105' r='1.3'/%3E%3Ccircle cx='417' cy='51' r='1.6'/%3E%3Ccircle cx='424' cy='70' r='1.7'/%3E%3Ccircle cx='465' cy='136' r='1.8'/%3E%3Ccircle cx='440' cy='82' r='2.0'/%3E%3Ccircle cx='396' cy='133' r='1.8'/%3E%3Ccircle cx='532' cy='120' r='1.8'/%3E%3Ccircle cx='517' cy='58' r='1.7'/%3E%3Ccircle cx='461' cy='147' r='1.9'/%3E%3Ccircle cx='520' cy='115' r='1.9'/%3E%3Ccircle cx='494' cy='129' r='1.5'/%3E%3Ccircle cx='434' cy='53' r='1.9'/%3E%3Ccircle cx='471' cy='120' r='1.7'/%3E%3Ccircle cx='493' cy='103' r='1.3'/%3E%3Ccircle cx='515' cy='136' r='1.7'/%3E%3Ccircle cx='466' cy='124' r='1.3'/%3E%3Ccircle cx='504' cy='72' r='1.4'/%3E%3Ccircle cx='417' cy='133' r='1.4'/%3E%3Ccircle cx='459' cy='89' r='1.6'/%3E%3Ccircle cx='494' cy='138' r='1.7'/%3E%3Ccircle cx='486' cy='50' r='1.4'/%3E%3Ccircle cx='415' cy='135' r='1.5'/%3E%3Ccircle cx='472' cy='42' r='1.3'/%3E%3Ccircle cx='417' cy='126' r='1.8'/%3E%3Ccircle cx='492' cy='77' r='1.7'/%3E%3Ccircle cx='453' cy='100' r='1.4'/%3E%3Ccircle cx='532' cy='66' r='2.0'/%3E%3Ccircle cx='452' cy='145' r='2.0'/%3E%3Ccircle cx='451' cy='74' r='1.4'/%3E%3Ccircle cx='475' cy='58' r='1.7'/%3E%3Ccircle cx='519' cy='105' r='1.9'/%3E%3Ccircle cx='497' cy='70' r='1.9'/%3E%3Ccircle cx='457' cy='43' r='1.3'/%3E%3Ccircle cx='458' cy='98' r='1.5'/%3E%3Ccircle cx='394' cy='84' r='1.5'/%3E%3Ccircle cx='421' cy='88' r='1.6'/%3E%3Ccircle cx='434' cy='95' r='1.5'/%3E%3Ccircle cx='377' cy='53' r='1.9'/%3E%3Ccircle cx='414' cy='74' r='1.7'/%3E%3Ccircle cx='403' cy='88' r='2.0'/%3E%3Ccircle cx='500' cy='46' r='1.8'/%3E%3Ccircle cx='451' cy='136' r='1.8'/%3E%3Ccircle cx='421' cy='46' r='1.9'/%3E%3Ccircle cx='391' cy='100' r='1.5'/%3E%3Ccircle cx='423' cy='135' r='2.0'/%3E%3Ccircle cx='416' cy='124' r='1.5'/%3E%3Ccircle cx='471' cy='90' r='1.4'/%3E%3Ccircle cx='398' cy='67' r='1.9'/%3E%3Ccircle cx='459' cy='68' r='1.9'/%3E%3Ccircle cx='394' cy='65' r='1.4'/%3E%3Ccircle cx='431' cy='52' r='1.5'/%3E%3Ccircle cx='416' cy='113' r='1.9'/%3E%3Ccircle cx='551' cy='228' r='1.6'/%3E%3Ccircle cx='536' cy='226' r='1.5'/%3E%3Ccircle cx='504' cy='221' r='2.0'/%3E%3Ccircle cx='509' cy='232' r='1.7'/%3E%3Ccircle cx='559' cy='218' r='1.5'/%3E%3Ccircle cx='517' cy='227' r='1.6'/%3E%3Ccircle cx='561' cy='231' r='1.7'/%3E%3Ccircle cx='510' cy='233' r='1.8'/%3E%3Ccircle cx='544' cy='245' r='1.6'/%3E%3Ccircle cx='553' cy='219' r='1.9'/%3E%3Ccircle cx='544' cy='223' r='1.4'/%3E%3Ccircle cx='517' cy='239' r='1.8'/%3E%3Ccircle cx='536' cy='236' r='1.6'/%3E%3Ccircle cx='515' cy='237' r='1.3'/%3E%3Ccircle cx='521' cy='230' r='2.0'/%3E%3Ccircle cx='544' cy='250' r='1.6'/%3E%3C/g%3E%3C/svg%3E");
    background-repeat: repeat-x;
    background-position: 0 50%;
    background-size: auto 170%;
    overflow: hidden;
    animation: worldMapPan 45s linear infinite;
  }
  .world-map-bg::before {
    content: '';
    position: absolute;
    inset: 0;
    background: radial-gradient(circle at 30% 35%, rgba(200,154,43,.14), transparent 62%);
    pointer-events: none;
  }
  @keyframes worldMapPan {
    from { background-position-x: 0; }
    to   { background-position-x: -640px; }
  }

  @media (max-width: 1024px) {
    .home-layout { grid-template-columns: minmax(0, 1fr) 280px; gap: 20px; }
  }
  @media (max-width: 820px) {
    .home-page { padding-block: 22px 38px; }
    .home-layout { grid-template-columns: 1fr; }
    .home-sidebar { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); }
    .home-sidebar .sidebar-widget, .home-sidebar .sidebar-ad { margin: 0; }
  }
  @media (max-width: 560px) {
    .home-section { margin-bottom: 26px; }
    .home-sidebar { grid-template-columns: 1fr; }
    .home-ad { min-height: 92px; border-radius: 10px; }
    .home-ad-media { height: 105px; }
    .home-ad-fallback { min-height: 92px; padding: 18px; }
  }
  @media (prefers-reduced-motion: reduce) {
    .ad-animate,
    .ad-animate img,
    .ad-animate video {
      opacity: 1;
      transform: none !important;
      transition: none !important;
      animation: none !important;
    }
    .ad-animate::after { display: none; }
    .world-map-bg { animation: none; }
  }
</style>

@if($breakingNews->count())
<div class="breaking-bar">
  <div class="container">
    <div class="breaking-label">{{ __('messages.breaking') }}</div>
    <div class="breaking-ticker">
      <div class="breaking-ticker-inner">
        @foreach($breakingNews as $b)
        <a href="{{ route('articles.show',$b->slug) }}" class="breaking-item" style="color:#fff">
          <span class="breaking-dot"></span>{{ $b->title }}
        </a>
        @endforeach
        @foreach($breakingNews as $b)
        <a href="{{ route('articles.show',$b->slug) }}" class="breaking-item" style="color:#fff">
          <span class="breaking-dot"></span>{{ $b->title }}
        </a>
        @endforeach
      </div>
    </div>
  </div>
</div>
@endif

<div class="main-content home-page">
<div class="container">

  {{-- Homepage Banner Ads: shown first so active campaigns are immediately visible. --}}
  @if($homepageAds->isNotEmpty())
  <section class="home-section home-ad-list" aria-label="{{ __('messages.advertisement') }}">
    @foreach($homepageAds as $ad)
    @php
      $homepageAdMedia = $adMediaUrl($adMediaValue($ad));
      $homepageAdIsVideo = $adIsVideo($ad, $homepageAdMedia);
    @endphp
    <article class="home-ad ad-animate" data-ad-reveal style="--ad-delay: {{ min($loop->index * 90, 360) }}ms">
      @if($ad->link)<a href="{{ $ad->link }}" target="_blank" rel="noopener noreferrer sponsored">@endif
        @if($homepageAdMedia && $homepageAdIsVideo)
        <video class="home-ad-media world-map-bg" autoplay muted loop playsinline preload="metadata"
               aria-label="{{ $ad->title }}"
               onerror="this.hidden=true;this.nextElementSibling.hidden=false">
          <source src="{{ $homepageAdMedia }}" type="{{ $adVideoMime($homepageAdMedia) }}">
        </video>
        <div class="home-ad-fallback" hidden>
          <span class="home-ad-label">{{ __('messages.advertisement') }}</span>
          <span class="home-ad-title">{{ $ad->title }}</span>
        </div>
        @elseif($homepageAdMedia)
        <img class="home-ad-media world-map-bg" src="{{ $homepageAdMedia }}" alt="{{ $ad->title }}" loading="eager"
             onerror="this.hidden=true;this.nextElementSibling.hidden=false">
        <div class="home-ad-fallback" hidden>
          <span class="home-ad-label">{{ __('messages.advertisement') }}</span>
          <span class="home-ad-title">{{ $ad->title }}</span>
        </div>
        @else
        <div class="home-ad-fallback world-map-bg">
          <span class="home-ad-label">{{ __('messages.advertisement') }}</span>
          <span class="home-ad-title">{{ $ad->title }}</span>
        </div>
        @endif
      @if($ad->link)</a>@endif
    </article>
    @endforeach
  </section>
  @endif

  {{-- Featured Hero --}}
  @if($featuredArticles->count())
  <section class="home-section">
    <div class="hero-grid">
      @if($featuredArticles->first())
      @php $hero = $featuredArticles->first() @endphp
      <div class="article-card-featured">
        <div class="card-img">
          @if($hero->main_image_url)
          <img src="{{ $hero->main_image_url }}" alt="{{ $hero->title }}" onerror="this.style.display='none'">
          @else
          <div class="world-map-bg" style="height:100%;display:flex;align-items:center;justify-content:center;position:relative">
            <i class="fa-solid fa-newspaper" style="font-size:64px;color:rgba(200,154,43,.35);position:relative;z-index:1"></i>
          </div>
          @endif
          <div style="position:absolute;inset:0;background:linear-gradient(to top,rgba(0,0,0,.7) 0%,transparent 60%)"></div>
          @if($hero->is_breaking)<div style="position:absolute;top:14px;{{ $isRtl ? 'right' : 'left' }}:14px"><span class="badge-breaking">{{ __('messages.breaking') }}</span></div>@endif
          @if($hero->is_featured)<div style="position:absolute;top:14px;{{ $isRtl ? 'left' : 'right' }}:14px"><span class="badge-featured">⭐ {{ __('messages.badge_featured') }}</span></div>@endif
        </div>
        <div class="card-body">
          <div class="article-cat"><a href="{{ route('categories.show',$hero->category?->slug??'#') }}">{{ $hero->category?->name }}</a></div>
          <h1 class="card-title"><a href="{{ route('articles.show',$hero->slug) }}" style="color:var(--white)">{{ $hero->title }}</a></h1>
          @if($hero->summary)<p style="color:rgba(255,255,255,.5);font-size:13.5px;line-height:1.7;margin-bottom:12px">{{ Str::limit($hero->summary,160) }}</p>@endif
          <div class="article-meta">
            @if($hero->journalist)<span><i class="fa-solid fa-user-pen"></i>{{ $hero->journalist->name }}</span>@endif
            <span><i class="fa-solid fa-clock"></i>{{ $hero->published_at?->diffForHumans() }}</span>
            <span><i class="fa-solid fa-eye"></i>{{ number_format($hero->views) }}</span>
          </div>
        </div>
      </div>
      @endif

      {{-- Side stories --}}
      <div style="display:flex;flex-direction:column;gap:12px">
        @foreach($featuredArticles->skip(1)->take(3) as $f)
        <div class="article-card" style="flex-direction:row;align-items:stretch">
          <div style="width:88px;flex-shrink:0;background:var(--surface2);border-radius:10px 0 0 10px;overflow:hidden">
            @if($f->main_image_url)
            <img src="{{ $f->main_image_url }}" alt="" style="width:100%;height:100%;object-fit:cover">
            @else
            <div class="world-map-bg" style="height:100%;display:flex;align-items:center;justify-content:center">
              <i class="fa-solid fa-newspaper" style="color:rgba(200,154,43,.4);font-size:18px;position:relative;z-index:1"></i>
            </div>
            @endif
          </div>
          <div class="article-card-body" style="padding:12px">
            <div class="article-cat">{{ $f->category?->name }}</div>
            <div class="article-title" style="font-size:13px"><a href="{{ route('articles.show',$f->slug) }}">{{ Str::limit($f->title,65) }}</a></div>
            <div class="article-meta"><span><i class="fa-solid fa-clock"></i>{{ $f->published_at?->diffForHumans() }}</span></div>
          </div>
        </div>
        @endforeach
      </div>
    </div>
  </section>
  @endif

  {{-- Main: Latest + Sidebar --}}
  <div class="home-layout">
    <div>
      {{-- Latest News --}}
      <div style="margin-bottom:32px">
        <div class="section-header">
          <div class="section-title"><i class="fa-solid fa-bolt"></i> {{ __('messages.section_latest') }}</div>
          <a href="{{ route('search') }}" class="section-more">{{ __('messages.section_more') }} <i class="fa-solid fa-chevron-{{ $isRtl ? 'left' : 'right' }}"></i></a>
        </div>
        <div class="articles-grid">
          @forelse($latestArticles->take(9) as $a)
          <div class="article-card">
            <div class="article-card-img">
              @if($a->main_image_url)
              <img src="{{ $a->main_image_url }}" alt="{{ $a->title }}" loading="lazy" onerror="this.style.display='none'">
              @else
              <div class="world-map-bg" style="height:100%;display:flex;align-items:center;justify-content:center">
                <i class="fa-solid fa-newspaper" style="font-size:32px;color:rgba(200,154,43,.35);position:relative;z-index:1"></i>
              </div>
              @endif
              <div class="badge-overlay">
                @if($a->is_breaking)<span class="badge-breaking">{{ __('messages.badge_breaking') }}</span>@endif
                @if($a->is_featured)<span class="badge-featured">{{ __('messages.badge_featured') }}</span>@endif
              </div>
            </div>
            <div class="article-card-body">
              <div class="article-cat"><a href="{{ route('categories.show',$a->category?->slug??'#') }}">{{ $a->category?->name }}</a></div>
              <div class="article-title"><a href="{{ route('articles.show',$a->slug) }}">{{ Str::limit($a->title,80) }}</a></div>
              <div class="article-meta">
                @if($a->journalist)<span><i class="fa-solid fa-user-pen"></i>{{ $a->journalist->name }}</span>@endif
                <span><i class="fa-solid fa-clock"></i>{{ $a->published_at?->diffForHumans() }}</span>
                <span><i class="fa-solid fa-eye"></i>{{ number_format($a->views) }}</span>
              </div>
            </div>
          </div>
          @empty
          <div style="grid-column:1/-1;text-align:center;padding:48px;color:rgba(255,255,255,.25)">
            <i class="fa-solid fa-newspaper" style="font-size:36px;margin-bottom:10px;display:block"></i>
            {{ __('messages.no_articles') }}
          </div>
          @endforelse
        </div>
      </div>

      {{-- Editor Picks --}}
      @if($editorPicks->count())
      <div style="margin-bottom:32px">
        <div class="section-header">
          <div class="section-title"><i class="fa-solid fa-pen-nib"></i> {{ __('messages.section_editor_picks') }}</div>
        </div>
        <div class="editor-picks-grid">
          @foreach($editorPicks as $ep)
          <div class="article-card" style="flex-direction:row;align-items:stretch">
            <div style="width:80px;flex-shrink:0;background:rgba(200,154,43,.1);border-radius:10px 0 0 10px;overflow:hidden;border-{{ $isRtl ? 'left' : 'right' }}:2px solid var(--gold)">
              @if($ep->main_image_url)
              <img src="{{ $ep->main_image_url }}" alt="" style="width:100%;height:100%;object-fit:cover">
              @else
              <div style="height:100%;display:flex;align-items:center;justify-content:center"><i class="fa-solid fa-pen-nib" style="color:var(--gold);opacity:.5;font-size:18px"></i></div>
              @endif
            </div>
            <div class="article-card-body" style="padding:12px">
              <div style="font-size:10px;font-weight:700;color:var(--gold);margin-bottom:4px;letter-spacing:.3px">✏ {{ __('messages.badge_editor_pick') }}</div>
              <div class="article-title" style="font-size:12.5px"><a href="{{ route('articles.show',$ep->slug) }}">{{ Str::limit($ep->title,65) }}</a></div>
              <div class="article-meta"><span>{{ $ep->published_at?->diffForHumans() }}</span></div>
            </div>
          </div>
          @endforeach
        </div>
      </div>
      @endif

      {{-- Videos --}}
      @if($featuredVideos->count())
      <div>
        <div class="section-header">
          <div class="section-title"><i class="fa-solid fa-video"></i> {{ __('messages.section_videos') }}</div>
          <a href="{{ route('videos.index') }}" class="section-more">{{ __('messages.section_more') }} <i class="fa-solid fa-chevron-{{ $isRtl ? 'left' : 'right' }}"></i></a>
        </div>
        <div class="articles-grid">
          @foreach($featuredVideos as $vid)
          <a href="{{ route('videos.show',$vid->slug) }}" class="article-card">
            <div class="article-card-img world-map-bg">
              @if($vid->thumbnail)
              <img src="{{ $vid->thumbnail }}" alt="{{ $vid->title }}" loading="lazy" onerror="this.style.display='none'" style="position:relative;z-index:1">
              @else
              <div style="height:100%;display:flex;align-items:center;justify-content:center;position:relative;z-index:1"><i class="fa-solid fa-play" style="font-size:36px;color:rgba(255,255,255,.3)"></i></div>
              @endif
              <div style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;z-index:2">
                <div style="width:48px;height:48px;background:rgba(200,154,43,.9);border-radius:50%;display:flex;align-items:center;justify-content:center">
                  <i class="fa-solid fa-play" style="color:var(--black);font-size:14px;margin-right:-2px"></i>
                </div>
              </div>
            </div>
            <div class="article-card-body">
              <div class="article-cat"><i class="fa-solid fa-video"></i> {{ $vid->category?->name ?? __('messages.section_videos') }}</div>
              <div class="article-title">{{ Str::limit($vid->title,68) }}</div>
              <div class="article-meta"><span><i class="fa-solid fa-eye"></i>{{ __('messages.views_count',['count'=>number_format($vid->views)]) }}</span></div>
            </div>
          </a>
          @endforeach
        </div>
      </div>
      @endif
    </div>

    {{-- Sidebar --}}
    <aside class="home-sidebar">
      {{-- Trending --}}
      <div class="sidebar-widget">
        <div class="widget-header"><span class="widget-title"><i class="fa-solid fa-fire"></i> {{ __('messages.section_trending') }}</span></div>
        <div class="widget-body">
          @forelse($trendingArticles->take(6) as $i => $t)
          <a href="{{ route('articles.show',$t->slug) }}" class="widget-article">
            <div style="font-size:20px;font-weight:900;color:{{ $i<3?'var(--gold)':'rgba(255,255,255,.2)' }};width:28px;flex-shrink:0;text-align:center;font-family:'Inter',sans-serif">{{ $i+1 }}</div>
            <div class="widget-article-body">
              <div class="widget-article-title">{{ Str::limit($t->title,68) }}</div>
              <div class="widget-article-meta"><i class="fa-solid fa-eye" style="color:var(--gold);opacity:.6"></i> {{ number_format($t->views) }}</div>
            </div>
          </a>
          @empty
          <div style="padding:20px;text-align:center;color:rgba(255,255,255,.25);font-size:13px">{{ __('messages.no_articles') }}</div>
          @endforelse
        </div>
      </div>

      {{-- Categories --}}
      <div class="sidebar-widget">
        <div class="widget-header"><span class="widget-title"><i class="fa-solid fa-folder"></i> {{ __('messages.all_categories') }}</span></div>
        <div class="widget-body" style="padding:8px">
          @foreach($categories as $cat)
          <a href="{{ route('categories.show',$cat->slug) }}"
             style="display:flex;justify-content:space-between;align-items:center;padding:9px 10px;border-radius:7px;transition:.15s;color:rgba(255,255,255,.6)">
            <span style="font-weight:600;font-size:13px">{{ $cat->name }}</span>
            <span style="background:rgba(200,154,43,.15);border:1px solid rgba(200,154,43,.2);padding:2px 8px;border-radius:10px;font-size:11px;color:var(--gold)">{{ $cat->articles_count }}</span>
          </a>
          @endforeach
        </div>
      </div>

      {{-- Sidebar Ads --}}
      @if($sidebarAds->isNotEmpty())
      @foreach($sidebarAds as $ad)
      @php
        $sidebarAdMedia = $adMediaUrl($adMediaValue($ad));
        $sidebarAdIsVideo = $adIsVideo($ad, $sidebarAdMedia);
      @endphp
      <div class="sidebar-ad ad-animate" data-ad-reveal style="--ad-delay: {{ min($loop->index * 80, 320) }}ms">
        @if($ad->link)<a href="{{ $ad->link }}" target="_blank" rel="noopener noreferrer sponsored">@endif
          @if($sidebarAdMedia && $sidebarAdIsVideo)
          <video class="sidebar-ad-media world-map-bg" autoplay muted loop playsinline preload="metadata"
                 aria-label="{{ $ad->title }}"
                 onerror="this.hidden=true;this.nextElementSibling.hidden=false">
            <source src="{{ $sidebarAdMedia }}" type="{{ $adVideoMime($sidebarAdMedia) }}">
          </video>
          <div class="home-ad-fallback" hidden>
            <span class="home-ad-label">{{ __('messages.advertisement') }}</span>
            <span class="home-ad-title">{{ $ad->title }}</span>
          </div>
          @elseif($sidebarAdMedia)
          <img class="sidebar-ad-media world-map-bg" src="{{ $sidebarAdMedia }}" alt="{{ $ad->title }}" loading="lazy"
               onerror="this.hidden=true;this.nextElementSibling.hidden=false">
          <div class="home-ad-fallback" hidden>
            <span class="home-ad-label">{{ __('messages.advertisement') }}</span>
            <span class="home-ad-title">{{ $ad->title }}</span>
          </div>
          @else
          <div class="home-ad-fallback world-map-bg">
            <span class="home-ad-label">{{ __('messages.advertisement') }}</span>
            <span class="home-ad-title">{{ $ad->title }}</span>
          </div>
          @endif
        @if($ad->link)</a>@endif
      </div>
      @endforeach
      @endif

      {{-- Newsletter mini --}}
      <div class="sidebar-widget" style="background:var(--surface2)">
        <div style="padding:22px;text-align:center">
          <i class="fa-solid fa-envelope-open-text" style="font-size:26px;color:var(--gold);margin-bottom:10px;display:block"></i>
          <div style="font-size:14px;font-weight:700;margin-bottom:5px;color:var(--white)">{{ __('messages.newsletter_widget_title') }}</div>
          <div style="font-size:12px;color:rgba(255,255,255,.4);margin-bottom:14px">{{ __('messages.newsletter_widget_sub') }}</div>
          <form action="{{ route('newsletter.subscribe') }}" method="POST">
            @csrf
            <input type="email" name="email" placeholder="{{ __('messages.newsletter_placeholder') }}" required
                   style="width:100%;padding:9px 12px;border-radius:7px;border:1px solid var(--border);font-family:'Cairo',sans-serif;font-size:13px;margin-bottom:8px;direction:{{ $isRtl ? 'rtl' : 'ltr' }};background:var(--surface);color:var(--white)">
            <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center">{{ __('messages.newsletter_btn') }}</button>
          </form>
        </div>
      </div>
    </aside>
  </div>

</div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    const ads = document.querySelectorAll('[data-ad-reveal]');

    if (!ads.length) {
      return;
    }

    if (
      window.matchMedia('(prefers-reduced-motion: reduce)').matches ||
      !('IntersectionObserver' in window)
    ) {
      ads.forEach(function (ad) {
        ad.classList.add('is-visible');
      });
      return;
    }

    const observer = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (!entry.isIntersecting) {
          return;
        }

        entry.target.classList.add('is-visible');
        observer.unobserve(entry.target);
      });
    }, {
      threshold: 0.14,
      rootMargin: '0px 0px -36px 0px'
    });

    ads.forEach(function (ad) {
      observer.observe(ad);
    });
  });
</script>
@endsection