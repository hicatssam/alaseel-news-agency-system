@php $locale = app()->getLocale(); $isRtl = $locale === 'ar'; $dir = $isRtl ? 'rtl' : 'ltr'; @endphp
<!DOCTYPE html>
<html lang="{{ $locale }}" dir="{{ $dir }}">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>@yield('title',__('messages.site_name')) — {{ __('messages.site_tagline') }}</title>
<meta name="description" content="@yield('description',__('messages.site_name'))">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700;900&family=Inter:wght@400;500;600;700&display=swap">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
*{box-sizing:border-box;margin:0;padding:0}
:root{
  --gold:#C89A2B;--gold-light:#F0C75E;--black:#0B0B0B;--surface:#151515;
  --surface2:#1B1B1B;--border:#2A2A2A;--white:#fff;--red:#D62828;
  --text:#E8E8E8;--text-muted:#B9B9B9
}
html[dir="rtl"] body{font-family:'Cairo',sans-serif}
html[dir="ltr"] body{font-family:'Inter',sans-serif}
body{background:var(--black);color:var(--text)}
a{text-decoration:none;color:inherit}
img{max-width:100%}

/* ── Top bar ─────────────────────────────────────────────── */
.topbar{background:var(--surface);border-bottom:1px solid var(--border);color:var(--text-muted);font-size:12px;padding:7px 0}
.topbar .container{display:flex;justify-content:space-between;align-items:center}
.topbar-date{display:flex;align-items:center;gap:6px;font-family:'Inter',sans-serif}
.topbar-social{display:flex;gap:14px}
.topbar-social a{color:rgba(255,255,255,.4);font-size:13px;transition:.2s}
.topbar-social a:hover{color:var(--gold)}

/* ── Header ─────────────────────────────────────────────── */
.site-header{background:var(--surface);border-bottom:1px solid var(--border);position:sticky;top:0;z-index:200;box-shadow:0 4px 24px rgba(0,0,0,.4)}
.header-inner{padding:14px 0;display:flex;align-items:center;justify-content:space-between;gap:20px}
.site-brand{display:flex;align-items:center;gap:14px;flex-shrink:0}
.brand-badge{width:48px;height:48px;background:var(--gold);border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:20px;font-weight:900;color:var(--black);font-family:'Inter',sans-serif}
.brand-name{font-size:20px;font-weight:900;color:var(--white);line-height:1.2}
.brand-tagline{font-size:11px;color:var(--gold);letter-spacing:.5px;opacity:.9}
.search-form{display:flex;gap:0;max-width:340px;flex:1}
.search-input{flex:1;padding:10px 14px;border:1px solid var(--border);font-size:13px;outline:none;background:var(--surface2);color:var(--white);transition:.2s}
html[dir="rtl"] .search-input{border-radius:8px 0 0 8px;font-family:'Cairo',sans-serif;direction:rtl}
html[dir="ltr"] .search-input{border-radius:0 8px 8px 0;font-family:'Inter',sans-serif;direction:ltr}
.search-input::placeholder{color:rgba(255,255,255,.3)}
.search-input:focus{border-color:var(--gold)}
html[dir="rtl"] .search-btn{border-radius:0 8px 8px 0}
html[dir="ltr"] .search-btn{border-radius:8px 0 0 8px}
.search-btn{background:var(--gold);color:var(--black);border:none;padding:10px 16px;cursor:pointer;font-size:14px;transition:.2s}
.search-btn:hover{background:var(--gold-light)}
.header-actions{display:flex;align-items:center;gap:10px;flex-shrink:0}
.btn-cta{display:flex;align-items:center;gap:6px;padding:9px 18px;background:var(--gold);color:var(--black);border-radius:8px;font-size:13px;font-weight:700;transition:.2s}
.btn-cta:hover{background:var(--gold-light)}

/* ── Language switcher ──────────────────────────────────── */
.lang-switcher{display:flex;align-items:center;gap:2px;background:rgba(255,255,255,.06);border:1px solid var(--border);border-radius:20px;padding:3px}
.lang-switcher a{padding:4px 10px;border-radius:14px;font-size:11px;font-weight:700;color:rgba(255,255,255,.45);font-family:'Inter',sans-serif;letter-spacing:.3px;transition:.15s;text-decoration:none}
.lang-switcher a:hover{color:var(--white)}
.lang-switcher a.active{background:var(--gold);color:var(--black)}

/* ── Navigation bar ─────────────────────────────────────── */
.navbar{background:var(--surface2);border-top:1px solid var(--border)}
.navbar .container{display:flex;align-items:center;gap:0}
.nav-links{display:flex;gap:0;list-style:none;margin:0;padding:0}
.nav-links li a{display:block;padding:13px 18px;color:rgba(255,255,255,.65);font-size:13.5px;font-weight:600;transition:.2s;border-bottom:2px solid transparent;white-space:nowrap}
.nav-links li a:hover,.nav-links li a.active{color:var(--gold);border-bottom-color:var(--gold)}
.hamburger{display:none;color:var(--white);font-size:20px;padding:13px;cursor:pointer}
html[dir="rtl"] .hamburger{margin-right:auto}
html[dir="ltr"] .hamburger{margin-left:auto}

/* ── Breaking bar ──────────────────────────────────────── */
.breaking-bar{background:var(--red);color:#fff;padding:9px 0;overflow:hidden}
.breaking-bar .container{display:flex;align-items:center;gap:12px}
.breaking-label{background:rgba(0,0,0,.25);padding:3px 12px;border-radius:4px;font-size:12px;font-weight:700;white-space:nowrap;letter-spacing:.3px}
.breaking-ticker{font-size:13px;white-space:nowrap;overflow:hidden;display:flex;gap:0;flex:1}
.breaking-ticker-inner{display:inline-flex;gap:0;animation:ticker 30s linear infinite}
.breaking-ticker-inner:hover{animation-play-state:paused}
.breaking-item{display:inline-flex;align-items:center;gap:8px;padding:0 20px}
.breaking-dot{width:6px;height:6px;background:rgba(255,255,255,.6);border-radius:50%}
@keyframes ticker{from{transform:translateX(0)}to{transform:translateX(-50%)}}

/* ── Container ──────────────────────────────────────────── */
.container{max-width:1240px;margin:0 auto;padding:0 20px}
.main-content{padding:32px 0}

/* ── Article Cards ───────────────────────────────────────── */
.articles-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:20px}
.articles-grid-4{display:grid;grid-template-columns:repeat(4,1fr);gap:16px}
.article-card{background:var(--surface);border:1px solid var(--border);border-radius:12px;overflow:hidden;transition:.2s;display:flex;flex-direction:column}
.article-card:hover{border-color:rgba(200,154,43,.4);box-shadow:0 8px 32px rgba(0,0,0,.5);transform:translateY(-2px)}
.article-card-img{position:relative;overflow:hidden;height:200px;background:var(--surface2)}
.article-card-img img{width:100%;height:100%;object-fit:cover;transition:.3s}
.article-card:hover .article-card-img img{transform:scale(1.04)}
.article-card-img .badge-overlay{position:absolute;top:10px;display:flex;gap:4px;flex-wrap:wrap}
html[dir="rtl"] .article-card-img .badge-overlay{right:10px}
html[dir="ltr"] .article-card-img .badge-overlay{left:10px}
.article-card-body{padding:16px;flex:1;display:flex;flex-direction:column}
.article-cat{font-size:11px;font-weight:700;color:var(--gold);letter-spacing:.5px;margin-bottom:6px;text-transform:uppercase}
.article-title{font-size:14.5px;font-weight:700;color:var(--white);line-height:1.55;margin-bottom:8px;flex:1}
.article-title a:hover{color:var(--gold)}
.article-meta{display:flex;align-items:center;gap:10px;font-size:11px;color:rgba(255,255,255,.4);flex-wrap:wrap}
.article-meta i{color:var(--gold);opacity:.7}
.badge-breaking{background:var(--red);color:#fff;font-size:10px;font-weight:700;padding:3px 7px;border-radius:4px}
.badge-featured{background:var(--gold);color:var(--black);font-size:10px;font-weight:700;padding:3px 7px;border-radius:4px}

/* ── Featured card ──────────────────────────────────────── */
.article-card-featured{background:var(--surface);border:1px solid var(--border);border-radius:14px;overflow:hidden;transition:.2s}
.article-card-featured:hover{border-color:rgba(200,154,43,.4);box-shadow:0 12px 40px rgba(0,0,0,.5)}
.article-card-featured .card-img{height:340px;overflow:hidden;background:var(--surface2);position:relative}
.article-card-featured .card-img img{width:100%;height:100%;object-fit:cover;transition:.3s}
.article-card-featured:hover .card-img img{transform:scale(1.03)}
.article-card-featured .card-body{padding:22px}
.article-card-featured .card-title{font-size:18px;font-weight:900;line-height:1.5;margin-bottom:8px;color:var(--white)}

/* ── Sidebar ──────────────────────────────────────────────── */
.sidebar-widget{background:var(--surface);border:1px solid var(--border);border-radius:12px;overflow:hidden;margin-bottom:20px}
.widget-header{padding:14px 18px;border-bottom:1px solid var(--border);display:flex;align-items:center;gap:8px;position:relative}
.widget-header::after{content:'';position:absolute;bottom:0;width:50px;height:2px;background:var(--gold)}
html[dir="rtl"] .widget-header::after{right:0}
html[dir="ltr"] .widget-header::after{left:0}
.widget-title{font-size:13.5px;font-weight:900;color:var(--white)}
.widget-title i{color:var(--gold)}
.widget-body{padding:0}
.widget-article{display:flex;gap:10px;padding:12px 16px;border-bottom:1px solid var(--border);transition:.15s}
.widget-article:hover{background:var(--surface2)}
.widget-article:last-child{border-bottom:none}
.widget-article-img{width:68px;height:58px;border-radius:6px;object-fit:cover;background:var(--surface2);flex-shrink:0}
.widget-article-body{flex:1}
.widget-article-title{font-size:12.5px;font-weight:700;color:var(--white);line-height:1.4;margin-bottom:4px}
.widget-article-meta{font-size:11px;color:rgba(255,255,255,.35)}

/* ── Section headers ─────────────────────────────────────── */
.section-header{display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;padding-bottom:12px;border-bottom:1px solid var(--border);position:relative}
.section-header::after{content:'';position:absolute;bottom:-1px;width:56px;height:2px;background:var(--gold)}
html[dir="rtl"] .section-header::after{right:0}
html[dir="ltr"] .section-header::after{left:0}
.section-title{font-size:16px;font-weight:900;color:var(--white)}
.section-title i{color:var(--gold)}
.section-more{font-size:12.5px;color:var(--gold);font-weight:600;display:flex;align-items:center;gap:4px}
.section-more:hover{color:var(--gold-light)}

/* ── Newsletter ──────────────────────────────────────────── */
.newsletter-section{background:var(--surface);border:1px solid var(--border);border-radius:14px;padding:36px;text-align:center;margin:28px 0;position:relative;overflow:hidden}
.newsletter-section::before{content:'';position:absolute;top:-60px;left:50%;transform:translateX(-50%);width:300px;height:300px;background:radial-gradient(circle,rgba(200,154,43,.12),transparent 70%);pointer-events:none}
.newsletter-title{font-size:20px;font-weight:900;margin-bottom:8px;color:var(--white)}
.newsletter-sub{font-size:13px;color:rgba(255,255,255,.5);margin-bottom:22px}
.newsletter-form{display:flex;gap:0;max-width:440px;margin:0 auto}
.newsletter-input{flex:1;border:1px solid var(--border);font-size:13px;outline:none;background:var(--surface2);color:var(--white);padding:12px 16px}
html[dir="rtl"] .newsletter-input{border-radius:8px 0 0 8px;font-family:'Cairo',sans-serif;direction:rtl}
html[dir="ltr"] .newsletter-input{border-radius:0 8px 8px 0;font-family:'Inter',sans-serif;direction:ltr}
.newsletter-input::placeholder{color:rgba(255,255,255,.3)}
.newsletter-input:focus{border-color:var(--gold)}
html[dir="rtl"] .newsletter-btn{border-radius:0 8px 8px 0;font-family:'Cairo',sans-serif}
html[dir="ltr"] .newsletter-btn{border-radius:8px 0 0 8px;font-family:'Inter',sans-serif}
.newsletter-btn{background:var(--gold);color:var(--black);border:none;padding:12px 20px;font-size:13.5px;font-weight:700;cursor:pointer;transition:.2s;white-space:nowrap}
.newsletter-btn:hover{background:var(--gold-light)}

/* ── Footer ─────────────────────────────────────────────── */
.site-footer{background:var(--surface);border-top:1px solid var(--border);margin-top:48px}
.footer-main{padding:44px 0;display:grid;grid-template-columns:2fr 1fr 1fr 1fr;gap:32px}
.footer-brand-name{font-size:17px;font-weight:900;color:var(--white);margin-bottom:10px;display:flex;align-items:center;gap:8px}
.footer-brand-badge{width:32px;height:32px;background:var(--gold);border-radius:6px;display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:900;color:var(--black);font-family:'Inter',sans-serif}
.footer-brand-desc{font-size:12.5px;color:rgba(255,255,255,.45);line-height:1.8}
.footer-title{font-size:13px;font-weight:700;color:var(--white);margin-bottom:16px;padding-bottom:8px;border-bottom:1px solid var(--border);letter-spacing:.3px}
.footer-links{list-style:none;display:flex;flex-direction:column;gap:9px}
.footer-links li a{font-size:12.5px;color:rgba(255,255,255,.45);transition:.2s;display:flex;align-items:center;gap:7px}
.footer-links li a:hover{color:var(--gold)}
.footer-links li a i{font-size:9px;color:rgba(255,255,255,.25)}
.footer-social{display:flex;gap:8px;margin-top:16px;flex-wrap:wrap}
.footer-social a{width:34px;height:34px;border-radius:7px;background:rgba(255,255,255,.06);border:1px solid var(--border);display:flex;align-items:center;justify-content:center;font-size:13px;color:rgba(255,255,255,.5);transition:.2s}
.footer-social a:hover{background:var(--gold);color:var(--black);border-color:var(--gold)}
.footer-bottom{border-top:1px solid var(--border);padding:16px 0;text-align:center;font-size:11.5px;color:rgba(255,255,255,.25)}

/* ── Buttons ──────────────────────────────────────────────── */
.btn{display:inline-flex;align-items:center;gap:6px;padding:10px 20px;border-radius:8px;font-size:13.5px;font-weight:700;cursor:pointer;border:none;transition:.2s}
html[dir="rtl"] .btn{font-family:'Cairo',sans-serif}
html[dir="ltr"] .btn{font-family:'Inter',sans-serif}
.btn-primary{background:var(--gold);color:var(--black)}.btn-primary:hover{background:var(--gold-light)}
.btn-outline{border:1px solid var(--border);color:var(--text-muted);background:transparent}.btn-outline:hover{border-color:var(--gold);color:var(--gold)}

/* ── Alerts ───────────────────────────────────────────────── */
.alert{padding:14px 18px;border-radius:8px;margin-bottom:16px;font-size:13.5px;display:flex;align-items:center;gap:10px}
html[dir="rtl"] .alert-success{background:rgba(39,174,96,.12);color:#6ee7b7;border-right:3px solid #27ae60}
html[dir="ltr"] .alert-success{background:rgba(39,174,96,.12);color:#6ee7b7;border-left:3px solid #27ae60}
html[dir="rtl"] .alert-error{background:rgba(214,40,40,.12);color:#fca5a5;border-right:3px solid #D62828}
html[dir="ltr"] .alert-error{background:rgba(214,40,40,.12);color:#fca5a5;border-left:3px solid #D62828}

/* ── Pagination ──────────────────────────────────────────── */
.pagination{display:flex;gap:4px;justify-content:center;margin-top:24px;flex-wrap:wrap}
.pagination a,.pagination span{padding:8px 14px;border-radius:8px;text-decoration:none;font-size:13px;border:1px solid var(--border);color:rgba(255,255,255,.5);background:var(--surface);display:flex;align-items:center;justify-content:center;transition:.2s}
.pagination a:hover{border-color:var(--gold);color:var(--gold)}
.pagination .active span,.pagination span.active{background:var(--gold);border-color:var(--gold);color:var(--black);font-weight:700}

/* ── Tags ──────────────────────────────────────────────────── */
.tags-cloud{display:flex;flex-wrap:wrap;gap:6px}
.tag-chip{padding:5px 12px;background:var(--surface2);border:1px solid var(--border);border-radius:20px;font-size:12px;color:rgba(255,255,255,.5);transition:.2s}
.tag-chip:hover{background:rgba(200,154,43,.15);border-color:var(--gold);color:var(--gold)}

/* ── Homepage layout grids (made responsive via classes) ─── */
.hero-grid{display:grid;grid-template-columns:1.8fr 1fr;gap:20px;align-items:start}
.main-grid{display:grid;grid-template-columns:1fr 310px;gap:28px;align-items:start}
.editor-picks-grid{display:grid;grid-template-columns:1fr 1fr;gap:14px}

/* ── Mobile nav ──────────────────────────────────────────── */
.mobile-nav{display:none;background:var(--surface);position:fixed;top:0;left:0;right:0;bottom:0;z-index:999;overflow-y:auto;padding:24px}
.mobile-nav.open{display:block}
.mobile-nav-close{color:var(--white);font-size:18px;cursor:pointer;margin-bottom:24px;display:flex;align-items:center;gap:8px;padding-bottom:16px;border-bottom:1px solid var(--border)}
.mobile-nav a{display:block;padding:13px 0;color:rgba(255,255,255,.7);font-size:15px;font-weight:600;border-bottom:1px solid var(--border)}
.mobile-nav a:hover{color:var(--gold)}

/* ── Responsive ──────────────────────────────────────────── */
@media(max-width:1024px){.articles-grid{grid-template-columns:repeat(2,1fr)}}
@media(max-width:768px){
  .articles-grid{grid-template-columns:1fr}
  .articles-grid-4{grid-template-columns:1fr 1fr}
  .footer-main{grid-template-columns:1fr 1fr}
  .header-inner{flex-wrap:wrap;gap:10px;padding:10px 0}
  .search-form{order:3;width:100%;max-width:100%}
  .hamburger{display:block}
  .nav-links{display:none}
  .newsletter-form{flex-direction:column}
  .newsletter-input,.newsletter-btn{border-radius:8px!important}
  .topbar .container{flex-wrap:wrap;gap:6px}
  /* Homepage grids → single column */
  .hero-grid{grid-template-columns:1fr!important}
  .main-grid{grid-template-columns:1fr!important}
  .editor-picks-grid{grid-template-columns:1fr!important}
  /* Tighten card images on mobile */
  .card-img{height:220px}
  .article-card-img{height:160px}
  /* Site brand logo */
  .site-brand img{height:44px}
}
@media(max-width:480px){
  .articles-grid-4{grid-template-columns:1fr}
  .footer-main{grid-template-columns:1fr}
  .container{padding:0 14px}
  .section-header{margin-bottom:14px}
  .topbar{display:none}
  .site-brand img{height:38px}
  .header-actions .lang-switcher a{padding:3px 8px;font-size:10px}
}
</style>
@stack('styles')
</head>
<body>
<!-- Top info bar -->
<div class="topbar">
  <div class="container">
    <div class="topbar-date">
      <i class="fa-regular fa-calendar" style="color:var(--gold)"></i>
      {{ now()->locale($locale)->isoFormat('dddd، D MMMM Y') }}
    </div>
    <div class="topbar-social">
      <a href="{{ $settings['facebook_url'] ?? '#' }}"><i class="fa-brands fa-facebook-f"></i></a>
      <a href="{{ $settings['twitter_url'] ?? '#' }}"><i class="fa-brands fa-x-twitter"></i></a>
      <a href="{{ $settings['youtube_url'] ?? '#' }}"><i class="fa-brands fa-youtube"></i></a>
      <a href="{{ $settings['instagram_url'] ?? '#' }}"><i class="fa-brands fa-instagram"></i></a>
      <a href="{{ $settings['telegram_url'] ?? '#' }}"><i class="fa-brands fa-telegram"></i></a>
    </div>
  </div>
</div>

<!-- Header -->
<header class="site-header">
  <div class="container">
    <div class="header-inner">
      <a href="{{ route('home') }}" class="site-brand">
        <img src="{{ asset('images/logo.png') }}" alt="{{ __('messages.site_name') }}"
             style="height:56px;width:auto;display:block;object-fit:contain">
      </a>
      <form action="{{ route('search') }}" method="GET" class="search-form">
        <input type="text" name="q" class="search-input" placeholder="{{ __('messages.search_placeholder') }}" value="{{ request('q') }}">
        <button type="submit" class="search-btn"><i class="fa-solid fa-search"></i></button>
      </form>
      <div class="header-actions">
        <div class="lang-switcher">
          <a href="{{ route('language.switch','ar') }}" class="{{ $locale==='ar'?'active':'' }}">ع</a>
          <a href="{{ route('language.switch','en') }}" class="{{ $locale==='en'?'active':'' }}">EN</a>
          <a href="{{ route('language.switch','fr') }}" class="{{ $locale==='fr'?'active':'' }}">FR</a>
        </div>
        @auth
        {{-- <a href="{{ route('admin.dashboard') }}" class="btn-cta"><i class="fa-solid fa-gauge-high"></i> {{ __('messages.btn_dashboard') }}</a>
        @else
        <a href="{{ route('login') }}" class="btn-cta"><i class="fa-solid fa-right-to-bracket"></i> {{ __('messages.btn_login') }}</a>
        @endauth --}}
      </div>
    </div>
  </div>
  <nav class="navbar">
    <div class="container">
      <button class="hamburger" onclick="document.getElementById('mobileNav').classList.toggle('open')">
        <i class="fa-solid fa-bars"></i>
      </button>
      <ul class="nav-links">
        <li><a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}">{{ __('messages.nav_home') }}</a></li>
        @isset($navCategories)
          @foreach($navCategories as $navCat)
          <li><a href="{{ route('categories.show', $navCat->slug) }}" class="{{ request()->is('category/'.$navCat->slug.'*') ? 'active' : '' }}">{{ $navCat->name }}</a></li>
          @endforeach
        @endisset
        <li><a href="{{ route('videos.index') }}" class="{{ request()->routeIs('videos.*') ? 'active' : '' }}">{{ __('messages.nav_videos') }}</a></li>
        <li>
          <a href="{{ route('live') }}" class="{{ request()->routeIs('live') ? 'active' : '' }}" style="display:flex;align-items:center;gap:6px">
            {{ __('messages.nav_live') }}
            @isset($activeStream)
            <span style="display:inline-flex;align-items:center;gap:4px;background:var(--red);color:#fff;font-size:10px;font-weight:700;padding:2px 7px;border-radius:10px;letter-spacing:.3px;animation:navLivePulse 1.8s ease infinite">
              <span style="width:5px;height:5px;border-radius:50%;background:#fff;display:inline-block"></span>{{ __('messages.live_badge') }}
            </span>
            @endisset
          </a>
        </li>
        <li><a href="{{ route('contact') }}" class="{{ request()->routeIs('contact*') ? 'active' : '' }}">{{ __('messages.nav_contact') }}</a></li>
      </ul>
    </div>
  </nav>
  <style>@keyframes navLivePulse{0%,100%{opacity:1}50%{opacity:.6}}</style>
</header>

<!-- Mobile Nav -->
<div class="mobile-nav" id="mobileNav">
  <div class="mobile-nav-close" onclick="document.getElementById('mobileNav').classList.remove('open')">
    <i class="fa-solid fa-times"></i> {{ __('messages.btn_close_menu') }}
  </div>
  <a href="{{ route('home') }}">{{ __('messages.nav_home') }}</a>
  @isset($navCategories)
    @foreach($navCategories as $navCat)
    <a href="{{ route('categories.show', $navCat->slug) }}">{{ $navCat->name }}</a>
    @endforeach
  @endisset
  <a href="{{ route('videos.index') }}">{{ __('messages.nav_videos') }}</a>
  <a href="{{ route('live') }}" style="display:flex;align-items:center;gap:6px">
    {{ __('messages.nav_live') }}
    @isset($activeStream)
    <span style="background:var(--red);color:#fff;font-size:10px;font-weight:700;padding:2px 7px;border-radius:10px">{{ __('messages.live_badge') }}</span>
    @endisset
  </a>
  <a href="{{ route('contact') }}">{{ __('messages.nav_contact') }}</a>
  <div style="display:flex;gap:8px;padding:16px 0;border-top:1px solid var(--border);margin-top:8px">
    <a href="{{ route('language.switch','ar') }}" style="padding:6px 14px;border-radius:6px;font-size:12px;font-weight:700;background:{{ $locale==='ar'?'var(--gold)':'var(--surface2)' }};color:{{ $locale==='ar'?'var(--black)':'rgba(255,255,255,.6)' }}">ع</a>
    <a href="{{ route('language.switch','en') }}" style="padding:6px 14px;border-radius:6px;font-size:12px;font-weight:700;background:{{ $locale==='en'?'var(--gold)':'var(--surface2)' }};color:{{ $locale==='en'?'var(--black)':'rgba(255,255,255,.6)' }}">EN</a>
    <a href="{{ route('language.switch','fr') }}" style="padding:6px 14px;border-radius:6px;font-size:12px;font-weight:700;background:{{ $locale==='fr'?'var(--gold)':'var(--surface2)' }};color:{{ $locale==='fr'?'var(--black)':'rgba(255,255,255,.6)' }}">FR</a>
  </div>
</div>

{{-- Header-position ads --}}
@isset($headerAds)
@if($headerAds->count())
<div style="background:var(--surface2);border-bottom:1px solid var(--border);padding:8px 0">
  <div class="container" style="display:flex;justify-content:center;align-items:center;gap:12px;flex-wrap:wrap">
    @foreach($headerAds as $ad)
    @if($ad->link)<a href="{{ $ad->link }}" target="_blank" rel="noopener noreferrer" style="display:block">@endif
      @if($ad->image)
      <img src="{{ $ad->image }}" alt="{{ $ad->title }}" style="max-height:80px;border-radius:6px;display:block">
      @else
      <div style="background:rgba(200,154,43,.07);border:1px dashed rgba(200,154,43,.25);border-radius:6px;padding:10px 20px;text-align:center">
        <span style="font-size:10px;color:rgba(255,255,255,.3);letter-spacing:.5px;display:block;margin-bottom:3px">Ad</span>
        <span style="color:rgba(255,255,255,.65);font-size:13px;font-weight:700">{{ $ad->title }}</span>
      </div>
      @endif
    @if($ad->link)</a>@endif
    @endforeach
  </div>
</div>
@endif
@endisset

<main>
  @if(session('success'))
  <div class="container" style="padding-top:16px">
    <div class="alert alert-success"><i class="fa-solid fa-circle-check"></i> {{ session('success') }}</div>
  </div>
  @endif
  @yield('content')
</main>

@section('newsletter_section')
<div class="container">
  <div class="newsletter-section">
    <i class="fa-solid fa-bell" style="font-size:28px;color:var(--gold);margin-bottom:12px;display:block"></i>
    <div class="newsletter-title">{{ __('messages.newsletter_title') }}</div>
    <div class="newsletter-sub">{{ __('messages.newsletter_sub') }}</div>
    <form action="{{ route('newsletter.subscribe') }}" method="POST" class="newsletter-form">
      @csrf
      <input type="email" name="email" class="newsletter-input" placeholder="{{ __('messages.newsletter_placeholder') }}" required>
      <button type="submit" class="newsletter-btn"><i class="fa-solid fa-paper-plane"></i> {{ __('messages.newsletter_btn') }}</button>
    </form>
  </div>
</div>
@show

{{-- Footer-position ads --}}
@isset($footerAds)
@if($footerAds->count())
<div style="padding:12px 0;border-top:1px solid var(--border);background:var(--surface2)">
  <div class="container" style="display:flex;justify-content:center;align-items:center;gap:12px;flex-wrap:wrap">
    @foreach($footerAds as $ad)
    @if($ad->link)<a href="{{ $ad->link }}" target="_blank" rel="noopener noreferrer" style="display:block">@endif
      @if($ad->image)
      <img src="{{ $ad->image }}" alt="{{ $ad->title }}" style="max-height:80px;border-radius:6px;display:block">
      @else
      <div style="background:rgba(200,154,43,.07);border:1px dashed rgba(200,154,43,.25);border-radius:6px;padding:10px 20px;text-align:center">
        <span style="font-size:10px;color:rgba(255,255,255,.3);letter-spacing:.5px;display:block;margin-bottom:3px">Ad</span>
        <span style="color:rgba(255,255,255,.65);font-size:13px;font-weight:700">{{ $ad->title }}</span>
      </div>
      @endif
    @if($ad->link)</a>@endif
    @endforeach
  </div>
</div>
@endif
@endisset

<footer class="site-footer">
  <div class="container">
    <div class="footer-main">
      <div>
        <div class="footer-brand-name">
          <img src="{{ asset('images/logo.png') }}" alt="{{ __('messages.site_name') }}"
               style="height:52px;width:auto;display:block;object-fit:contain">
        </div>
        <div class="footer-brand-desc">{{ $settings['footer_text'] ?? __('messages.footer_brand_desc') }}</div>
        <div class="footer-social">
          <a href="{{ $settings['facebook_url'] ?? '#' }}"><i class="fa-brands fa-facebook-f"></i></a>
          <a href="{{ $settings['twitter_url'] ?? '#' }}"><i class="fa-brands fa-x-twitter"></i></a>
          <a href="{{ $settings['youtube_url'] ?? '#' }}"><i class="fa-brands fa-youtube"></i></a>
          <a href="{{ $settings['instagram_url'] ?? '#' }}"><i class="fa-brands fa-instagram"></i></a>
          <a href="{{ $settings['telegram_url'] ?? '#' }}"><i class="fa-brands fa-telegram"></i></a>
        </div>
      </div>
      <div>
        <div class="footer-title">{{ __('messages.footer_categories') }}</div>
        <ul class="footer-links">
          @isset($footerCategories)
            @foreach($footerCategories->take(6) as $fc)
            <li><a href="{{ route('categories.show', $fc->slug) }}"><i class="fa-solid fa-angle-right"></i> {{ $fc->name }}</a></li>
            @endforeach
          @endisset
        </ul>
      </div>
      <div>
        <div class="footer-title">{{ __('messages.footer_useful_links') }}</div>
        <ul class="footer-links">
          <li><a href="{{ route('home') }}"><i class="fa-solid fa-angle-right"></i> {{ __('messages.nav_home') }}</a></li>
          <li><a href="{{ route('videos.index') }}"><i class="fa-solid fa-angle-right"></i> {{ __('messages.nav_videos') }}</a></li>
          <li><a href="{{ route('search') }}"><i class="fa-solid fa-angle-right"></i> {{ __('messages.footer_search') }}</a></li>
          <li><a href="{{ route('contact') }}"><i class="fa-solid fa-angle-right"></i> {{ __('messages.nav_contact') }}</a></li>
        </ul>
      </div>
      <div>
        <div class="footer-title">{{ __('messages.footer_contact_us') }}</div>
        <ul class="footer-links">
          <li><a href="mailto:{{ $settings['site_email'] ?? 'info@alaseel.news' }}"><i class="fa-solid fa-envelope"></i> {{ $settings['site_email'] ?? 'info@alaseel.news' }}</a></li>
          <li><a href="#"><i class="fa-solid fa-phone"></i> {{ $settings['site_phone'] ?? __('messages.phone_value') }}</a></li>
          <li><a href="#"><i class="fa-solid fa-location-dot"></i> {{ $settings['site_address'] ?? __('messages.address_value') }}</a></li>
        </ul>
      </div>
    </div>
  </div>
  <div class="footer-bottom">
    <div class="container">© {{ date('Y') }} {{ __('messages.site_name') }} — {{ __('messages.footer_copyright') }} THE VOICE OF TRUTH</div>
  </div>
</footer>

@stack('scripts')
</body>
</html>
