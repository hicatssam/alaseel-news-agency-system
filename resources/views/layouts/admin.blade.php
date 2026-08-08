@php
$locale = app()->getLocale(); $isRtl = $locale === 'ar'; $dir = $isRtl ? 'rtl' : 'ltr';

// Role flags for sidebar visibility (super-admin / editor / journalist)
$isSuperAdmin = auth()->user()->hasRole('super-admin');
$isEditor     = auth()->user()->hasRole('editor');
$isJournalist = auth()->user()->hasRole('journalist');
@endphp
<!DOCTYPE html>
<html lang="{{ $locale }}" dir="{{ $dir }}">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>@yield('title',__('admin.dashboard_title')) - {{ __('messages.site_name') }}</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700;900&family=Inter:wght@400;500;600;700&display=swap">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<style>
*{box-sizing:border-box;margin:0;padding:0}
html[dir="rtl"] body{font-family:'Cairo',sans-serif}
html[dir="ltr"] body{font-family:'Inter',sans-serif}
body{background:#f0f2f5;color:#1a1a2e}
:root{--gold:#c9a84c;--gold-dark:#a67c00;--dark:#1a1a2e;--sidebar-w:260px;--header-h:64px}

/* Sidebar */
.sidebar{position:fixed;top:0;width:var(--sidebar-w);height:100vh;background:var(--dark);overflow-y:auto;z-index:100;transition:.3s}
html[dir="rtl"] .sidebar{right:0}
html[dir="ltr"] .sidebar{left:0}
.sidebar-logo{padding:20px;border-bottom:1px solid rgba(201,168,76,.2);display:flex;align-items:center;gap:12px}
.sidebar-logo .logo-text{color:var(--gold);font-size:14px;font-weight:700;line-height:1.4}
.sidebar-logo .logo-sub{color:rgba(255,255,255,.5);font-size:11px}
.sidebar-nav{padding:16px 0}
.nav-section{padding:8px 20px 4px;font-size:11px;color:rgba(255,255,255,.35);text-transform:uppercase;letter-spacing:.5px;font-weight:600}
.nav-item{display:flex;align-items:center;gap:10px;padding:11px 20px;color:rgba(255,255,255,.7);text-decoration:none;font-size:13.5px;transition:.2s;cursor:pointer}
html[dir="rtl"] .nav-item{border-right:3px solid transparent}
html[dir="ltr"] .nav-item{border-left:3px solid transparent}
html[dir="rtl"] .nav-item:hover,html[dir="rtl"] .nav-item.active{color:#fff;background:rgba(201,168,76,.12);border-right-color:var(--gold)}
html[dir="ltr"] .nav-item:hover,html[dir="ltr"] .nav-item.active{color:#fff;background:rgba(201,168,76,.12);border-left-color:var(--gold)}
.nav-item i{width:18px;text-align:center;font-size:14px;color:var(--gold);opacity:.8}
.nav-item.active i{opacity:1}
.badge-count{background:var(--gold);color:var(--dark);font-size:10px;font-weight:700;padding:2px 6px;border-radius:10px}
html[dir="rtl"] .badge-count{margin-right:auto}
html[dir="ltr"] .badge-count{margin-left:auto}

/* Header */
.header{position:fixed;top:0;height:var(--header-h);background:#fff;border-bottom:1px solid #e8e8e8;display:flex;align-items:center;justify-content:space-between;padding:0 24px;z-index:99;box-shadow:0 1px 4px rgba(0,0,0,.06)}
html[dir="rtl"] .header{right:var(--sidebar-w);left:0}
html[dir="ltr"] .header{left:var(--sidebar-w);right:0}
.header-left{display:flex;align-items:center;gap:16px}
.page-title{font-size:17px;font-weight:700;color:var(--dark)}
.header-actions{display:flex;align-items:center;gap:12px}
.header-btn{width:36px;height:36px;border-radius:8px;border:1px solid #e8e8e8;background:#fff;display:flex;align-items:center;justify-content:center;cursor:pointer;color:#666;font-size:15px;transition:.2s;position:relative;text-decoration:none}
.header-btn:hover{background:#f5f5f5;color:var(--gold)}
.user-menu{display:flex;align-items:center;gap:10px;padding:6px 12px;border-radius:8px;cursor:pointer;border:1px solid #e8e8e8;background:#fff;transition:.2s}
.user-menu:hover{background:#f5f5f5}
.user-avatar{width:36px;height:36px;min-width:36px;background:var(--gold);border-radius:50%;display:flex;align-items:center;justify-content:center;color:var(--dark);font-weight:700;font-size:13px;overflow:hidden;border:2px solid rgba(201,168,76,.35)}
.user-avatar img{width:100%;height:100%;display:block;object-fit:cover;object-position:center}
.user-info{min-width:0}
.user-name{font-size:13px;font-weight:600;color:var(--dark)}
.user-role{font-size:11px;color:#888}

/* Language switcher (admin) */
.admin-lang{display:flex;align-items:center;gap:1px;background:#f0f2f5;border:1px solid #e0e0e0;border-radius:16px;padding:2px}
.admin-lang a{padding:3px 10px;border-radius:12px;font-size:11px;font-weight:700;color:#888;font-family:'Inter',sans-serif;letter-spacing:.3px;transition:.15s;text-decoration:none}
.admin-lang a:hover{color:var(--dark)}
.admin-lang a.active{background:var(--gold);color:var(--dark)}

/* Main */
.main{margin-top:var(--header-h);min-height:calc(100vh - var(--header-h));padding:24px}
html[dir="rtl"] .main{margin-right:var(--sidebar-w)}
html[dir="ltr"] .main{margin-left:var(--sidebar-w)}

/* Cards */
.card{background:#fff;border-radius:12px;box-shadow:0 1px 4px rgba(0,0,0,.06);overflow:hidden}
.card-header{padding:16px 20px;border-bottom:1px solid #f0f0f0;display:flex;align-items:center;justify-content:space-between}
.card-title{font-size:15px;font-weight:700;color:var(--dark)}
.card-body{padding:20px}

/* Stats */
.stats-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:16px;margin-bottom:24px}
.stat-card{background:#fff;border-radius:12px;padding:20px;box-shadow:0 1px 4px rgba(0,0,0,.06);display:flex;align-items:center;gap:16px}
html[dir="rtl"] .stat-card{border-right:4px solid var(--gold)}
html[dir="ltr"] .stat-card{border-left:4px solid var(--gold)}
.stat-icon{width:48px;height:48px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:20px}
.stat-value{font-size:24px;font-weight:900;color:var(--dark)}
.stat-label{font-size:12px;color:#888;font-weight:500}
.stat-change{font-size:11px;margin-top:2px}
.stat-change.up{color:#27ae60}.stat-change.down{color:#e74c3c}

/* Table */
.table-wrap{overflow-x:auto}
table{width:100%;border-collapse:collapse}
thead th{background:#f8f9fa;padding:12px 14px;font-size:12px;font-weight:700;color:#555;border-bottom:2px solid #e8e8e8}
html[dir="rtl"] thead th{text-align:right}
html[dir="ltr"] thead th{text-align:left}
tbody td{padding:12px 14px;border-bottom:1px solid #f0f0f0;font-size:13.5px;color:#333;vertical-align:middle}
tbody tr:hover{background:#fafafa}
tbody tr:last-child td{border-bottom:none}

/* Badges */
.badge{display:inline-flex;align-items:center;gap:4px;padding:3px 8px;border-radius:20px;font-size:11px;font-weight:600}
.badge-success{background:#d4edda;color:#155724}
.badge-warning{background:#fff3cd;color:#856404}
.badge-danger{background:#f8d7da;color:#721c24}
.badge-info{background:#d1ecf1;color:#0c5460}
.badge-secondary{background:#e2e3e5;color:#383d41}
.badge-gold{background:rgba(201,168,76,.15);color:var(--gold-dark)}

/* Buttons */
.btn{display:inline-flex;align-items:center;gap:6px;padding:8px 16px;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;border:none;transition:.2s;text-decoration:none;white-space:nowrap}
html[dir="rtl"] .btn{font-family:'Cairo',sans-serif}
html[dir="ltr"] .btn{font-family:'Inter',sans-serif}
.btn-primary{background:var(--gold);color:var(--dark)}.btn-primary:hover{background:var(--gold-dark);color:#fff}
.btn-danger{background:#e74c3c;color:#fff}.btn-danger:hover{background:#c0392b}
.btn-secondary{background:#6c757d;color:#fff}.btn-secondary:hover{background:#545b62}
.btn-success{background:#27ae60;color:#fff}.btn-success:hover{background:#219a52}
.btn-outline{background:transparent;border:1px solid #dee2e6;color:#555}.btn-outline:hover{background:#f8f9fa}
.btn-sm{padding:5px 10px;font-size:12px}
.btn-icon{width:32px;height:32px;padding:0;justify-content:center;border-radius:6px}

/* Forms */
.form-group{margin-bottom:18px}
.form-label{display:block;font-size:13px;font-weight:600;color:#333;margin-bottom:6px}
.form-control{width:100%;padding:10px 12px;border:1px solid #dee2e6;border-radius:8px;font-size:13.5px;color:#333;background:#fff;transition:.2s}
html[dir="rtl"] .form-control{font-family:'Cairo',sans-serif;direction:rtl}
html[dir="ltr"] .form-control{font-family:'Inter',sans-serif;direction:ltr}
.form-control:focus{outline:none;border-color:var(--gold);box-shadow:0 0 0 3px rgba(201,168,76,.15)}
select.form-control{cursor:pointer}
textarea.form-control{resize:vertical;min-height:120px}
.form-check{display:flex;align-items:center;gap:8px;cursor:pointer}
.form-check input[type=checkbox]{width:16px;height:16px;cursor:pointer;accent-color:var(--gold)}
.form-row{display:grid;grid-template-columns:1fr 1fr;gap:16px}
.form-row-3{display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px}

/* Alerts */
.alert{padding:12px 16px;border-radius:8px;margin-bottom:16px;font-size:13.5px;display:flex;align-items:flex-start;gap:10px}
html[dir="rtl"] .alert-success{background:#d4edda;color:#155724;border-right:4px solid #28a745}
html[dir="ltr"] .alert-success{background:#d4edda;color:#155724;border-left:4px solid #28a745}
html[dir="rtl"] .alert-error,html[dir="rtl"] .alert-danger{background:#f8d7da;color:#721c24;border-right:4px solid #dc3545}
html[dir="ltr"] .alert-error,html[dir="ltr"] .alert-danger{background:#f8d7da;color:#721c24;border-left:4px solid #dc3545}
html[dir="rtl"] .alert-warning{background:#fff3cd;color:#856404;border-right:4px solid #ffc107}
html[dir="ltr"] .alert-warning{background:#fff3cd;color:#856404;border-left:4px solid #ffc107}
html[dir="rtl"] .alert-info{background:#d1ecf1;color:#0c5460;border-right:4px solid #17a2b8}
html[dir="ltr"] .alert-info{background:#d1ecf1;color:#0c5460;border-left:4px solid #17a2b8}

/* Pagination */
.pagination{display:flex;gap:4px;justify-content:center;margin-top:20px;flex-wrap:wrap}
.pagination a,.pagination span{padding:6px 12px;border-radius:6px;text-decoration:none;font-size:13px;border:1px solid #dee2e6;color:#555;background:#fff;display:flex;align-items:center;justify-content:center}
.pagination a:hover{background:#f0f2f5;border-color:var(--gold);color:var(--gold-dark)}
.pagination .active span,.pagination span.active{background:var(--gold);border-color:var(--gold);color:var(--dark);font-weight:700}

/* Filter bar */
.filter-bar{background:#fff;border-radius:10px;padding:16px;margin-bottom:18px;box-shadow:0 1px 4px rgba(0,0,0,.06);display:flex;gap:12px;align-items:center;flex-wrap:wrap}
.filter-bar .form-control{flex:1;min-width:180px}

/* Breadcrumb */
.breadcrumb{display:flex;align-items:center;gap:6px;font-size:12.5px;color:#888;margin-bottom:6px}
.breadcrumb a{color:var(--gold);text-decoration:none}
.breadcrumb .sep{color:#ccc}

/* Empty state */
.empty-state{text-align:center;padding:60px 20px;color:#999}
.empty-state i{font-size:48px;margin-bottom:12px;opacity:.3}
.empty-state p{font-size:14px}

/* Chart */
.chart-container{position:relative;height:240px}

/* Dropdown */
.dropdown{position:relative;display:inline-block}
.dropdown-menu{position:absolute;top:calc(100% + 4px);background:#fff;border:1px solid #e8e8e8;border-radius:10px;min-width:180px;box-shadow:0 8px 24px rgba(0,0,0,.12);z-index:200;overflow:hidden;display:none}
html[dir="rtl"] .dropdown-menu{left:0}
html[dir="ltr"] .dropdown-menu{right:0}
.dropdown:hover .dropdown-menu,.dropdown.open .dropdown-menu{display:block}
.dropdown-item{display:flex;align-items:center;gap:8px;padding:10px 14px;font-size:13px;color:#333;text-decoration:none;transition:.15s}
html[dir="rtl"] .dropdown-item{text-align:right}
html[dir="ltr"] .dropdown-item{text-align:left}
.dropdown-menu[dir="rtl"]{direction:rtl;text-align:right}
.dropdown-menu[dir="rtl"] .dropdown-item{direction:rtl;justify-content:flex-start;text-align:right}
.dropdown-item:hover{background:#f8f9fa;color:var(--gold)}
.dropdown-item i{width:14px;color:#999}
.dropdown-divider{height:1px;background:#f0f0f0;margin:4px 0}

/* Notification bell */
.notif-btn{position:relative}
.notif-badge{position:absolute;top:-5px;font-size:10px;font-weight:700;background:#e74c3c;color:#fff;border-radius:10px;min-width:18px;height:18px;display:flex;align-items:center;justify-content:center;padding:0 4px;border:2px solid #fff;pointer-events:none}
html[dir="rtl"] .notif-badge{left:-5px}
html[dir="ltr"] .notif-badge{right:-5px}
.notif-panel{position:absolute;top:calc(100% + 6px);background:#fff;border:1px solid #e8e8e8;border-radius:12px;width:340px;box-shadow:0 12px 32px rgba(0,0,0,.13);z-index:300;display:none;overflow:hidden}
html[dir="rtl"] .notif-panel{left:0}
html[dir="ltr"] .notif-panel{right:0}
.notif-btn.open .notif-panel{display:block}
.notif-panel-head{display:flex;align-items:center;justify-content:space-between;padding:14px 16px;border-bottom:1px solid #f0f0f0}
.notif-panel-head strong{font-size:14px;font-weight:700;color:#1a1a2e}
.notif-panel-head a{font-size:12px;color:var(--gold);text-decoration:none;font-weight:600}
.notif-panel-head a:hover{text-decoration:underline}
.notif-list{max-height:340px;overflow-y:auto}
.notif-item{display:flex;align-items:flex-start;gap:12px;padding:12px 16px;border-bottom:1px solid #f5f5f5;cursor:pointer;transition:.15s;text-decoration:none;color:inherit}
.notif-item:hover{background:#fafafa}
.notif-item.unread{background:rgba(201,168,76,.05)}
.notif-icon{width:34px;height:34px;border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:.8rem;margin-top:2px}
.notif-text{flex:1;min-width:0}
.notif-title{font-size:13px;font-weight:600;color:#1a1a2e;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.notif-item.unread .notif-title{font-weight:700}
.notif-msg{font-size:11.5px;color:#888;margin-top:2px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.notif-time{font-size:10.5px;color:#bbb;margin-top:4px}
.notif-dot{width:7px;height:7px;border-radius:50%;background:#e74c3c;flex-shrink:0;margin-top:6px}
.notif-panel-foot{padding:10px 16px;text-align:center;border-top:1px solid #f0f0f0}
.notif-panel-foot form{display:inline}
.notif-panel-foot button{background:none;border:none;font-size:12px;color:#888;cursor:pointer;padding:0}
.notif-panel-foot button:hover{color:var(--gold)}
.notif-empty{padding:32px 16px;text-align:center;color:#bbb;font-size:13px}

/* Mobile hamburger + overlay */
.hamburger-btn{display:none;align-items:center;justify-content:center;width:36px;height:36px;border-radius:8px;border:1px solid #e8e8e8;background:#fff;cursor:pointer;font-size:16px;color:#444;flex-shrink:0;transition:.2s}
.hamburger-btn:hover{background:#f5f5f5;color:var(--gold)}
.sidebar-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,.45);z-index:99;backdrop-filter:blur(2px)}
.sidebar-overlay.active{display:block}
.sidebar-close{display:none;position:absolute;top:14px;background:none;border:none;cursor:pointer;color:rgba(255,255,255,.5);font-size:18px;padding:4px 8px;line-height:1;transition:.2s;z-index:1}
html[dir="rtl"] .sidebar-close{left:10px}
html[dir="ltr"] .sidebar-close{right:10px}
.sidebar-close:hover{color:#fff}
@media(max-width:768px){
html[dir="rtl"] .sidebar{transform:translateX(100%)}
html[dir="ltr"] .sidebar{transform:translateX(-100%)}
.sidebar.open{transform:translateX(0)!important}
html[dir="rtl"] .main{margin-right:0}
html[dir="ltr"] .main{margin-left:0}
html[dir="rtl"] .header{right:0}
html[dir="ltr"] .header{left:0}
.stats-grid{grid-template-columns:1fr 1fr}
.form-row,.form-row-3{grid-template-columns:1fr}
.hamburger-btn{display:flex}
.sidebar-close{display:block}
.header-actions .btn-primary .btn-label{display:none}
}
@media(max-width:480px){
.stats-grid{grid-template-columns:1fr}
.header-actions{gap:6px}
}
</style>
@stack('styles')
</head>
<body>
<div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>
<aside class="sidebar">
  <div class="sidebar-logo" style="justify-content:center;padding:14px 16px;position:relative">
    <button class="sidebar-close" onclick="closeSidebar()" aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
    <a href="{{ route('home') }}" target="_blank" style="display:block">
     @include('partials.site-logo', [
    'class' => 'site-logo',
    'style' => 'max-width:180px;max-height:70px;object-fit:contain'
])
    </a>
  </div>
  <nav class="sidebar-nav">
    <div class="nav-section">{{ __('admin.section_home') }}</div>
    <a href="{{ route('admin.dashboard') }}" class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
      <i class="fa-solid fa-gauge-high"></i> {{ __('admin.nav_dashboard') }}
    </a>
    <a href="{{ route('home') }}" target="_blank" class="nav-item">
      <i class="fa-solid fa-globe"></i> {{ __('admin.nav_view_site') }}
    </a>

    <div class="nav-section">{{ __('admin.section_content') }}</div>

    {{-- Articles: all roles (journalist sees only his own via controller filtering) --}}
    <a href="{{ route('admin.articles.index') }}" class="nav-item {{ request()->routeIs('admin.articles.*') ? 'active' : '' }}">
      <i class="fa-solid fa-newspaper"></i> {{ __('admin.nav_articles') }}
    </a>

    {{-- Categories: super-admin & editor only --}}
    @if($isSuperAdmin || $isEditor)
    <a href="{{ route('admin.categories.index') }}" class="nav-item {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
      <i class="fa-solid fa-folder-tree"></i> {{ __('admin.nav_categories') }}
    </a>
    @endif

    {{-- Tags: super-admin & editor only --}}
    @if($isSuperAdmin || $isEditor)
    <a href="{{ route('admin.tags.index') }}" class="nav-item {{ request()->routeIs('admin.tags.*') ? 'active' : '' }}">
      <i class="fa-solid fa-tags"></i> {{ __('admin.nav_tags') }}
    </a>
    @endif

    {{-- Journalists management: super-admin & editor only --}}
    @if($isSuperAdmin || $isEditor)
    <a href="{{ route('admin.journalists.index') }}" class="nav-item {{ request()->routeIs('admin.journalists.*') ? 'active' : '' }}">
      <i class="fa-solid fa-user-tie"></i> {{ __('admin.nav_journalists') }}
    </a>
    @endif

    {{-- Videos: all roles --}}
    <a href="{{ route('admin.videos.index') }}" class="nav-item {{ request()->routeIs('admin.videos.*') ? 'active' : '' }}">
      <i class="fa-solid fa-video"></i> {{ __('admin.nav_videos') }}
    </a>

    {{-- Live stream: super-admin & editor only --}}
    @if($isSuperAdmin || $isEditor)
    <a href="{{ route('admin.live-streams.index') }}" class="nav-item {{ request()->routeIs('admin.live-streams.*') ? 'active' : '' }}">
      <i class="fa-solid fa-tower-broadcast"></i> {{ __('admin.nav_live_stream') }}
    </a>
    @endif

    {{-- Media library: all roles --}}
    <a href="{{ route('admin.media.index') }}" class="nav-item {{ request()->routeIs('admin.media.*') ? 'active' : '' }}">
      <i class="fa-solid fa-photo-film"></i> {{ __('admin.nav_media') }}
    </a>

    {{-- Engagement section: super-admin & editor only --}}
    @if($isSuperAdmin || $isEditor)
    <div class="nav-section">{{ __('admin.section_engagement') }}</div>
    <a href="{{ route('admin.comments.index') }}" class="nav-item {{ request()->routeIs('admin.comments.*') ? 'active' : '' }}">
      <i class="fa-solid fa-comments"></i> {{ __('admin.nav_comments') }}
    </a>
    <a href="{{ route('admin.contact.index') }}" class="nav-item {{ request()->routeIs('admin.contact.*') ? 'active' : '' }}">
      <i class="fa-solid fa-envelope"></i> {{ __('admin.nav_contact_messages') }}
    </a>
    <a href="{{ route('admin.newsletter.index') }}" class="nav-item {{ request()->routeIs('admin.newsletter.*') ? 'active' : '' }}">
      <i class="fa-solid fa-bell"></i> {{ __('admin.nav_newsletter') }}
    </a>
    @endif

    {{-- Marketing section: super-admin only (ads) --}}
    @if($isSuperAdmin)
    <div class="nav-section">{{ __('admin.section_marketing') }}</div>
    <a href="{{ route('admin.advertisements.index') }}" class="nav-item {{ request()->routeIs('admin.advertisements.*') ? 'active' : '' }}">
      <i class="fa-solid fa-rectangle-ad"></i> {{ __('admin.nav_advertisements') }}
    </a>
    @endif

    {{-- Notifications: all roles --}}
    <a href="{{ route('admin.notifications.index') }}" class="nav-item {{ request()->routeIs('admin.notifications.*') ? 'active' : '' }}">
      <i class="fa-solid fa-bell"></i> {{ __('admin.notif_title') }}
      @if($adminUnreadCount > 0)
      <span class="badge-count">{{ $adminUnreadCount }}</span>
      @endif
    </a>

    {{-- Administration section: super-admin only --}}
    @if($isSuperAdmin)
    <div class="nav-section">{{ __('admin.section_admin') }}</div>
    <a href="{{ route('admin.users.index') }}" class="nav-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
      <i class="fa-solid fa-users"></i> {{ __('admin.nav_users') }}
    </a>
    <a href="{{ route('admin.activity-logs.index') }}" class="nav-item {{ request()->routeIs('admin.activity-logs.*') ? 'active' : '' }}">
      <i class="fa-solid fa-clock-rotate-left"></i> {{ __('admin.nav_activity_logs') }}
    </a>
    <a href="{{ route('admin.settings.index') }}" class="nav-item {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
      <i class="fa-solid fa-gears"></i> {{ __('admin.nav_settings') }}
    </a>
    @endif
  </nav>
</aside>

<header class="header">
  <div class="header-left">
    <button class="hamburger-btn" onclick="toggleSidebar()" aria-label="Menu">
      <i class="fa-solid fa-bars"></i>
    </button>
    <div>
      <div class="breadcrumb">
        <a href="{{ route('admin.dashboard') }}">{{ __('admin.breadcrumb_home') }}</a>
        @hasSection('breadcrumb')
        <span class="sep">›</span>
        @yield('breadcrumb')
        @endif
      </div>
      <div class="page-title">@yield('title',__('admin.dashboard_title'))</div>
    </div>
  </div>
  <div class="header-actions">
    <div class="admin-lang">
      <a href="{{ route('language.switch','ar') }}" class="{{ $locale==='ar'?'active':'' }}">ع</a>
      <a href="{{ route('language.switch','en') }}" class="{{ $locale==='en'?'active':'' }}">EN</a>
      <a href="{{ route('language.switch','fr') }}" class="{{ $locale==='fr'?'active':'' }}">FR</a>
    </div>
    <a href="{{ route('admin.articles.create') }}" class="btn btn-primary btn-sm">
      <i class="fa-solid fa-plus"></i> <span class="btn-label">{{ __('admin.btn_new_article') }}</span>
    </a>
    <a href="{{ route('home') }}" target="_blank" class="header-btn" title="{{ __('admin.nav_view_site') }}">
      <i class="fa-solid fa-arrow-up-right-from-square"></i>
    </a>
{{-- Notification bell --}}
@php
    $typeIcon = [
        'article'    => 'fa-newspaper',
        'contact'    => 'fa-envelope',
        'user'       => 'fa-user',
        'comment'    => 'fa-comment',
        'newsletter' => 'fa-bell',
    ];

    $typeColor = [
        'article'    => '#c89a2b',
        'contact'    => '#3498db',
        'user'       => '#27ae60',
        'comment'    => '#e67e22',
        'newsletter' => '#9b59b6',
    ];

    $typeUrl = [
        'article'    => route('admin.articles.index'),
        'contact'    => route('admin.contact.index'),
        'user'       => route('admin.users.index'),
        'comment'    => route('admin.comments.index'),
        'newsletter' => route('admin.newsletter.index'),
    ];
@endphp

<div
    class="notif-btn header-btn"
    id="notifBtn"
    data-latest-id="{{ (int) ($adminRecentNotifications->max('id') ?? 0) }}"
    onclick="toggleNotif(event)"
    title="{{ __('admin.notif_title') }}"
    style="position:relative;cursor:pointer"
>
    <i class="fa-solid fa-bell"></i>

    <span
        class="notif-badge"
        id="notifBadge"
        style="{{ $adminUnreadCount > 0 ? '' : 'display:none' }}"
    >
        {{ $adminUnreadCount > 99 ? '99+' : $adminUnreadCount }}
    </span>

    <div
        class="notif-panel"
        id="notifPanel"
        onclick="event.stopPropagation()"
    >
        <div class="notif-panel-head">
            <strong>{{ __('admin.notif_title') }}</strong>

            <a href="{{ route('admin.notifications.index') }}">
                {{ __('admin.notif_view_all') }}
            </a>
        </div>

        <div class="notif-list" id="notificationList">
            @forelse($adminRecentNotifications as $notif)
                @php
                    $ni = $typeIcon[$notif->type] ?? 'fa-circle-info';
                    $nc = $typeColor[$notif->type] ?? '#888';
                    $nu = $typeUrl[$notif->type]
                        ?? route('admin.notifications.index');
                @endphp

                <a
                    href="{{ $nu }}"
                    class="notif-item {{ $notif->read_at ? '' : 'unread' }}"
                    data-notification-id="{{ $notif->id }}"
                    onclick="markRead(
                        event,
                        {{ $notif->id }},
                        @js($nu)
                    )"
                >
                    <div
                        class="notif-icon"
                        style="background:{{ $nc }}22"
                    >
                        <i
                            class="fa-solid {{ $ni }}"
                            style="color:{{ $nc }}"
                        ></i>
                    </div>

                    <div class="notif-text">
                        <div class="notif-title">
                            {{ $notif->title }}
                        </div>

                        <div class="notif-msg">
                            {{ $notif->message }}
                        </div>

                        <div class="notif-time">
                            {{ $notif->created_at?->diffForHumans() }}
                        </div>
                    </div>

                    @if(!$notif->read_at)
                        <div class="notif-dot"></div>
                    @endif
                </a>
            @empty
                <div class="notif-empty" id="notificationEmpty">
                    <i
                        class="fa-solid fa-bell-slash"
                        style="font-size:1.5rem;margin-bottom:8px;display:block;opacity:.3"
                    ></i>

                    {{ __('admin.notif_empty') }}
                </div>
            @endforelse
        </div>

        <div
            class="notif-panel-foot"
            id="notificationPanelFooter"
            style="{{ $adminUnreadCount > 0 ? '' : 'display:none' }}"
        >
            <form
                method="POST"
                action="{{ route('admin.notifications.read-all') }}"
            >
                @csrf

                <button type="submit">
                    <i class="fa-solid fa-check-double"></i>
                    {{ __('admin.notif_mark_all_read') }}
                </button>
            </form>
        </div>
    </div>
</div>

<button type="button" class="header-btn" id="notificationSoundButton"
        title="كتم صوت الإشعارات" aria-label="تشغيل أو كتم صوت الإشعارات">
    <i class="fa-solid fa-volume-high" id="notificationSoundIcon"></i>
</button>

{{-- User profile dropdown --}}
@php
    $headerUser = auth()->user();
    $headerProfilePath = $headerUser->profile_image ?? $headerUser->avatar ?? $headerUser->image ?? null;

    if ($headerProfilePath) {
        $headerProfileImage = \Illuminate\Support\Str::startsWith($headerProfilePath, ['http://', 'https://'])
            ? $headerProfilePath
            : asset(\Illuminate\Support\Str::startsWith($headerProfilePath, ['storage/', '/storage/'])
                ? ltrim($headerProfilePath, '/')
                : 'storage/' . ltrim($headerProfilePath, '/'));
    } else {
        $headerProfileImage = asset('images/default-avatar.png');
    }

    $profileViewUrl = \Illuminate\Support\Facades\Route::has('admin.profile.show')
        ? route('admin.profile.show') : '#';
    $settingsEditUrl = \Illuminate\Support\Facades\Route::has('admin.settings.update')
        ? route('admin.settings.update') : $profileViewUrl;
@endphp

<div class="dropdown" dir="rtl">
    <div class="user-menu">
        <div class="user-avatar">
            <img src="{{ $headerProfileImage }}" alt="صورة {{ $headerUser->name }}"
                 onerror="this.onerror=null;this.src='{{ asset('images/default-avatar.png') }}';">
        </div>

        <div class="user-info">
            <div class="user-name">{{ $headerUser->name }}</div>
            <div class="user-role">
                {{ $headerUser->roles->first()?->display_name
                    ?? $headerUser->roles->first()?->name
                    ?? 'مستخدم' }}
            </div>
        </div>

        <i class="fa-solid fa-chevron-down" style="font-size:10px;color:#999"></i>
    </div>

    <div class="dropdown-menu" dir="rtl">
        <a href="{{ $profileViewUrl }}" class="dropdown-item">
            <i class="fa-solid fa-eye"></i>
            <span>عرض الملف الشخصي</span>
        </a>

      <a href="{{ $settingsEditUrl }}" class="dropdown-item">
    <i class="fa-solid fa-gear"></i>
    <span>الإعدادات</span>
</a>

        <div class="dropdown-divider"></div>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="dropdown-item"
                    style="width:100%;border:0;background:none;cursor:pointer">
                <i class="fa-solid fa-right-from-bracket"></i>
                <span>تسجيل الخروج</span>
            </button>
        </form>
    </div>
</div>
  </div>
</header>

<main class="main">
  @if(session('success'))
  <div class="alert alert-success"><i class="fa-solid fa-circle-check"></i> {{ session('success') }}</div>
  @endif
  @if(session('error'))
  <div class="alert alert-error"><i class="fa-solid fa-circle-xmark"></i> {{ session('error') }}</div>
  @endif
  @if($errors->any())
  <div class="alert alert-danger">
    <i class="fa-solid fa-triangle-exclamation"></i>
    <ul style="margin:0;padding-inline-start:16px">
      @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
    </ul>
  </div>
  @endif
  @yield('content')
</main>

<script>
document.querySelectorAll('.dropdown').forEach(d => {
  d.querySelector('.user-menu')?.addEventListener('click', () => d.classList.toggle('open'));
});
document.addEventListener('click', e => {
  document.querySelectorAll('.dropdown.open').forEach(d => {
    if (!d.contains(e.target)) d.classList.remove('open');
  });
});
function toggleSidebar() {
  document.querySelector('.sidebar').classList.toggle('open');
  document.getElementById('sidebarOverlay').classList.toggle('active');
}
function closeSidebar() {
  document.querySelector('.sidebar').classList.remove('open');
  document.getElementById('sidebarOverlay').classList.remove('active');
}
document.addEventListener('keydown', e => { if (e.key === 'Escape') { closeSidebar(); closeNotif(); } });


const notifBtn = document.getElementById('notifBtn');
const notifBadge = document.getElementById('notifBadge');
const notificationList = document.getElementById('notificationList');
const notificationPanelFooter = document.getElementById(
    'notificationPanelFooter'
);
const soundButton = document.getElementById(
    'notificationSoundButton'
);
const soundIcon = document.getElementById(
    'notificationSoundIcon'
);

const csrfToken = document.querySelector(
    'meta[name="csrf-token"]'
)?.content;

const notificationCheckUrl =
    @json(route('admin.notifications.check-new'));

const notificationReadUrlTemplate =
    @json(route('admin.notifications.read', ['notification' => '__ID__']));

const notificationIndexUrl =
    @json(route('admin.notifications.index'));

const notificationTypeConfig = {
    article: {
        icon: 'fa-newspaper',
        color: '#c89a2b',
        url: @json(route('admin.articles.index'))
    },
    contact: {
        icon: 'fa-envelope',
        color: '#3498db',
        url: @json(route('admin.contact.index'))
    },
    user: {
        icon: 'fa-user',
        color: '#27ae60',
        url: @json(route('admin.users.index'))
    },
    comment: {
        icon: 'fa-comment',
        color: '#e67e22',
        url: @json(route('admin.comments.index'))
    },
    newsletter: {
        icon: 'fa-bell',
        color: '#9b59b6',
        url: @json(route('admin.newsletter.index'))
    }
};

let latestNotificationId = Number(
    notifBtn?.dataset.latestId || 0
);

let notificationChecking = false;
let audioContext = null;

let notificationSoundEnabled =
    localStorage.getItem('admin_notification_sound') !== 'muted';

function toggleNotif(event) {
    event.stopPropagation();
    notifBtn?.classList.toggle('open');
}

function closeNotif() {
    notifBtn?.classList.remove('open');
}

document.addEventListener('click', function (event) {
    if (notifBtn && !notifBtn.contains(event.target)) {
        closeNotif();
    }
});

function escapeHtml(value) {
    const element = document.createElement('div');
    element.textContent = value ?? '';
    return element.innerHTML;
}

function updateSoundIcon() {
    if (!soundIcon) {
        return;
    }

    soundIcon.className = notificationSoundEnabled
        ? 'fa-solid fa-volume-high'
        : 'fa-solid fa-volume-xmark';

    if (soundButton) {
        soundButton.title = notificationSoundEnabled
            ? 'كتم صوت الإشعارات'
            : 'تشغيل صوت الإشعارات';
    }
}

async function unlockNotificationAudio() {
    if (!notificationSoundEnabled) {
        return;
    }

    const AudioContextClass =
        window.AudioContext || window.webkitAudioContext;

    if (!AudioContextClass) {
        return;
    }

    if (!audioContext) {
        audioContext = new AudioContextClass();
    }

    if (audioContext.state === 'suspended') {
        await audioContext.resume();
    }
}

async function playNotificationSound() {
    if (!notificationSoundEnabled) {
        return;
    }

    await unlockNotificationAudio();

    if (!audioContext || audioContext.state !== 'running') {
        return;
    }

    const now = audioContext.currentTime;
    const gain = audioContext.createGain();
    const firstTone = audioContext.createOscillator();
    const secondTone = audioContext.createOscillator();

    firstTone.type = 'sine';
    secondTone.type = 'sine';

    firstTone.frequency.setValueAtTime(660, now);
    secondTone.frequency.setValueAtTime(880, now + 0.14);

    gain.gain.setValueAtTime(0.0001, now);
    gain.gain.exponentialRampToValueAtTime(0.25, now + 0.02);
    gain.gain.exponentialRampToValueAtTime(
        0.0001,
        now + 0.55
    );

    firstTone.connect(gain);
    secondTone.connect(gain);
    gain.connect(audioContext.destination);

    firstTone.start(now);
    firstTone.stop(now + 0.18);

    secondTone.start(now + 0.14);
    secondTone.stop(now + 0.5);
}

soundButton?.addEventListener('click', async function (event) {
    event.preventDefault();
    event.stopPropagation();

    notificationSoundEnabled = !notificationSoundEnabled;

    localStorage.setItem(
        'admin_notification_sound',
        notificationSoundEnabled ? 'enabled' : 'muted'
    );

    updateSoundIcon();

    if (notificationSoundEnabled) {
        await playNotificationSound();
    }
});

document.addEventListener(
    'click',
    unlockNotificationAudio,
    { once: true }
);

document.addEventListener(
    'keydown',
    unlockNotificationAudio,
    { once: true }
);

function updateNotificationBadge(count) {
    const unreadCount = Number(count || 0);

    if (notifBadge) {
        notifBadge.textContent =
            unreadCount > 99 ? '99+' : String(unreadCount);

        notifBadge.style.display =
            unreadCount > 0 ? 'flex' : 'none';
    }

    if (notificationPanelFooter) {
        notificationPanelFooter.style.display =
            unreadCount > 0 ? 'block' : 'none';
    }
}

function createNotificationElement(notification) {
    const config = notificationTypeConfig[notification.type] || {
        icon: 'fa-circle-info',
        color: '#888888',
        url: notificationIndexUrl
    };

    const item = document.createElement('a');

    item.href = config.url;
    item.className = 'notif-item unread';
    item.dataset.notificationId = notification.id;

    item.innerHTML = `
        <div
            class="notif-icon"
            style="background:${config.color}22"
        >
            <i
                class="fa-solid ${config.icon}"
                style="color:${config.color}"
            ></i>
        </div>

        <div class="notif-text">
            <div class="notif-title">
                ${escapeHtml(notification.title)}
            </div>

            <div class="notif-msg">
                ${escapeHtml(notification.message)}
            </div>

            <div class="notif-time">
                ${escapeHtml(notification.created_at)}
            </div>
        </div>

        <div class="notif-dot"></div>
    `;

    item.addEventListener('click', function (event) {
        markRead(event, notification.id, config.url);
    });

    return item;
}

function addNotificationsToList(notifications) {
    if (!notificationList) {
        return;
    }

    notificationList.querySelector('.notif-empty')?.remove();

    /*
     * الخادم يعيد الأحدث أولًا، لذلك نعكسها قبل الإضافة
     * حتى تبقى أحدث رسالة في أعلى القائمة.
     */
    [...notifications].reverse().forEach(function (notification) {
        const exists = notificationList.querySelector(
            `[data-notification-id="${notification.id}"]`
        );

        if (!exists) {
            notificationList.prepend(
                createNotificationElement(notification)
            );
        }
    });

    const items = notificationList.querySelectorAll('.notif-item');

    items.forEach(function (item, index) {
        if (index >= 10) {
            item.remove();
        }
    });
}

async function markRead(event, id, url) {
    event.preventDefault();

    const readUrl = notificationReadUrlTemplate.replace(
        '__ID__',
        String(id)
    );

    try {
        await fetch(readUrl, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin'
        });
    } finally {
        window.location.href = url;
    }
}

async function checkNewNotifications() {
    if (notificationChecking || document.hidden) {
        return;
    }

    notificationChecking = true;

    try {
        const url = new URL(
            notificationCheckUrl,
            window.location.origin
        );

        url.searchParams.set(
            'after_id',
            String(latestNotificationId)
        );

        const response = await fetch(url.toString(), {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin',
            cache: 'no-store'
        });

        if (!response.ok) {
            console.error(
                'Notification request failed:',
                response.status
            );

            return;
        }

        const data = await response.json();

        updateNotificationBadge(data.unread_count);

        const notifications = Array.isArray(data.notifications)
            ? data.notifications
            : [];

        if (notifications.length > 0) {
            addNotificationsToList(notifications);

            latestNotificationId = Math.max(
                latestNotificationId,
                Number(data.latest_id || 0)
            );

            if (notifBtn) {
                notifBtn.dataset.latestId =
                    String(latestNotificationId);
            }

            /*
             * صوت واحد للدُفعة الجديدة حتى لو وصلت
             * عدة إشعارات في اللحظة نفسها.
             */
            await playNotificationSound();
        }
    } catch (error) {
        console.error(
            'Notification check failed:',
            error
        );
    } finally {
        notificationChecking = false;
    }
}

document.addEventListener('visibilitychange', function () {
    if (!document.hidden) {
        checkNewNotifications();
    }
});

updateSoundIcon();

/*
 * فحص مباشر عند تحميل الصفحة، ثم كل 5 ثوانٍ.
 */
checkNewNotifications();
window.setInterval(checkNewNotifications, 5000);


</script>
@stack('scripts')
</body>
</html>