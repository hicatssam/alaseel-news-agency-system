@extends('layouts.app')
@section('title',__('messages.nav_home'))
@section('content')
@php $isRtl = app()->getLocale() === 'ar'; @endphp

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

<div class="main-content">
<div class="container">

  {{-- Featured Hero --}}
  @if($featuredArticles->count())
  <div style="margin-bottom:36px">
    <div class="hero-grid">
      @if($featuredArticles->first())
      @php $hero = $featuredArticles->first() @endphp
      <div class="article-card-featured">
        <div class="card-img">
          @if($hero->main_image)
          <img src="{{ $hero->main_image }}" alt="{{ $hero->title }}" onerror="this.style.display='none'">
          @else
          <div style="height:100%;background:linear-gradient(135deg,#151515,#1B1B1B);display:flex;align-items:center;justify-content:center;position:relative">
            <i class="fa-solid fa-newspaper" style="font-size:64px;color:rgba(200,154,43,.15)"></i>
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
            @if($f->main_image)
            <img src="{{ $f->main_image }}" alt="" style="width:100%;height:100%;object-fit:cover">
            @else
            <div style="height:100%;display:flex;align-items:center;justify-content:center"><i class="fa-solid fa-newspaper" style="color:rgba(200,154,43,.2);font-size:18px"></i></div>
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
  </div>
  @endif

  {{-- Homepage Banner Ads --}}
  @if($homepageAds->count())
  <div style="margin-bottom:28px;display:flex;flex-direction:column;gap:10px">
    @foreach($homepageAds as $ad)
    <div style="border-radius:10px;overflow:hidden;border:1px solid var(--border)">
      @if($ad->link)<a href="{{ $ad->link }}" target="_blank" rel="noopener noreferrer" style="display:block">@endif
        @if($ad->image)
        <img src="{{ $ad->image }}" alt="{{ $ad->title }}" style="width:100%;max-height:120px;object-fit:cover;display:block">
        @else
        <div style="background:rgba(200,154,43,.07);padding:18px 24px;text-align:center">
          <span style="font-size:11px;color:rgba(255,255,255,.3);letter-spacing:.5px;display:block;margin-bottom:4px">{{ __('messages.advertisement') }}</span>
          <span style="color:rgba(255,255,255,.7);font-size:14px;font-weight:700">{{ $ad->title }}</span>
        </div>
        @endif
      @if($ad->link)</a>@endif
    </div>
    @endforeach
  </div>
  @endif

  {{-- Main: Latest + Sidebar --}}
  <div class="main-grid">
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
              @if($a->main_image)
              <img src="{{ $a->main_image }}" alt="{{ $a->title }}" loading="lazy" onerror="this.style.display='none'">
              @else
              <div style="height:100%;display:flex;align-items:center;justify-content:center;background:var(--surface2)"><i class="fa-solid fa-newspaper" style="font-size:32px;color:rgba(200,154,43,.15)"></i></div>
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
              @if($ep->main_image)
              <img src="{{ $ep->main_image }}" alt="" style="width:100%;height:100%;object-fit:cover">
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
            <div class="article-card-img" style="background:#000">
              @if($vid->thumbnail)
              <img src="{{ $vid->thumbnail }}" alt="{{ $vid->title }}" loading="lazy" onerror="this.style.display='none'">
              @else
              <div style="height:100%;display:flex;align-items:center;justify-content:center;background:var(--surface2)"><i class="fa-solid fa-play" style="font-size:36px;color:rgba(255,255,255,.15)"></i></div>
              @endif
              <div style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center">
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
    <aside>
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
      @if($sidebarAds->count())
      @foreach($sidebarAds as $ad)
      <div class="sidebar-widget" style="overflow:visible">
        @if($ad->link)<a href="{{ $ad->link }}" target="_blank" rel="noopener noreferrer" style="display:block">@endif
          @if($ad->image)
          <img src="{{ $ad->image }}" alt="{{ $ad->title }}" style="width:100%;border-radius:12px;display:block">
          @else
          <div style="background:rgba(200,154,43,.07);border-radius:12px;padding:20px;text-align:center">
            <span style="font-size:10px;color:rgba(255,255,255,.3);letter-spacing:.5px;display:block;margin-bottom:5px">{{ __('messages.advertisement') }}</span>
            <span style="color:rgba(255,255,255,.65);font-size:13px;font-weight:700">{{ $ad->title }}</span>
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
@endsection
