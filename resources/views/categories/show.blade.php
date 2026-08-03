@extends('layouts.app')
@section('title', $category->name)
@section('content')
@php $isRtl = app()->getLocale() === 'ar'; @endphp
<div class="main-content">
<div class="container">
  <div style="background:linear-gradient(135deg,var(--surface),var(--surface2));color:var(--white);border:1px solid var(--border);border-radius:12px;padding:28px;margin-bottom:28px">
    <div style="font-size:12px;color:rgba(255,255,255,.4);margin-bottom:8px">
      <a href="{{ route('home') }}" style="color:rgba(255,255,255,.4)">{{ __('messages.nav_home') }}</a>
      / {{ __('messages.categories_breadcrumb') }}
    </div>
    <h1 style="font-size:26px;font-weight:900">{{ $category->name }}</h1>
    @if($category->description)<p style="color:rgba(255,255,255,.6);margin-top:8px">{{ $category->description }}</p>@endif
    <div style="margin-top:12px;font-size:13px;color:rgba(255,255,255,.4)">{{ __('messages.article_count',['count'=>$articles->total()]) }}</div>
  </div>

  <div style="display:grid;grid-template-columns:1fr 300px;gap:28px;align-items:start">
    <div>
      <div class="articles-grid">
        @forelse($articles as $a)
        <div class="article-card">
          <div class="article-card-img">
            @if($a->main_image)
            <img src="{{ $a->main_image }}" alt="{{ $a->title }}" loading="lazy" onerror="this.style.display='none'">
            @else
            <div style="height:100%;display:flex;align-items:center;justify-content:center;background:var(--surface2)"><i class="fa-solid fa-newspaper" style="font-size:36px;color:rgba(200,154,43,.15)"></i></div>
            @endif
            <div class="badge-overlay">
              @if($a->is_breaking)<span class="badge-breaking">{{ __('messages.badge_breaking') }}</span>@endif
            </div>
          </div>
          <div class="article-card-body">
            <div class="article-title"><a href="{{ route('articles.show',$a->slug) }}">{{ Str::limit($a->title,80) }}</a></div>
            <div class="article-meta">
              @if($a->journalist)<span><i class="fa-solid fa-user-pen"></i>{{ $a->journalist->name }}</span>@endif
              <span><i class="fa-solid fa-clock"></i>{{ $a->published_at?->diffForHumans() }}</span>
              <span><i class="fa-solid fa-eye"></i>{{ number_format($a->views) }}</span>
            </div>
          </div>
        </div>
        @empty
        <div style="grid-column:1/-1;text-align:center;padding:60px;color:rgba(255,255,255,.25)">
          <i class="fa-solid fa-newspaper" style="font-size:48px;margin-bottom:12px;display:block;opacity:.3"></i>
          {{ __('messages.no_articles_category') }}
        </div>
        @endforelse
      </div>
      @if($articles->hasPages())<div>{{ $articles->links() }}</div>@endif
    </div>
    <aside>
      <div class="sidebar-widget">
        <div class="widget-header"><span class="widget-title"><i class="fa-solid fa-folder"></i> {{ __('messages.all_categories') }}</span></div>
        <div class="widget-body" style="padding:12px">
          @foreach($categories as $cat)
          <a href="{{ route('categories.show',$cat->slug) }}" style="display:flex;justify-content:space-between;align-items:center;padding:8px 10px;border-radius:6px;color:rgba(255,255,255,.6);font-size:13.5px;font-weight:600;transition:.15s">
            <span>{{ $cat->name }}</span>
            <span style="background:rgba(200,154,43,.12);border:1px solid rgba(200,154,43,.2);padding:2px 8px;border-radius:10px;font-size:11px;color:var(--gold)">{{ $cat->articles_count }}</span>
          </a>
          @endforeach
        </div>
      </div>
    </aside>
  </div>
</div>
</div>
@endsection
