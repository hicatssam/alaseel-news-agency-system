@extends('layouts.app')
@section('title',__('messages.videos_page_title'))
@section('content')
<div class="main-content">
<div class="container">
  <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:24px">
    <h1 style="font-size:22px;font-weight:900;color:var(--white)"><i class="fa-solid fa-video" style="color:var(--gold)"></i> {{ __('messages.videos_page_title') }}</h1>
  </div>

  @if($featured->count())
  <div style="margin-bottom:28px">
    <div class="section-header"><div class="section-title">{{ __('messages.section_featured_videos') }}</div></div>
    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:16px">
      @foreach($featured as $v)
      <a href="{{ route('videos.show',$v->slug) }}" class="article-card">
        <div class="article-card-img" style="background:#000">
          @if($v->thumbnail)
          <img src="{{ $v->thumbnail }}" alt="{{ $v->title }}" loading="lazy" onerror="this.style.display='none'">
          @endif
          <div style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center">
            <div style="width:52px;height:52px;background:rgba(200,154,43,.9);border-radius:50%;display:flex;align-items:center;justify-content:center"><i class="fa-solid fa-play" style="color:var(--black);font-size:18px;margin-right:-2px"></i></div>
          </div>
          <div style="position:absolute;top:10px;right:10px"><span class="badge-featured">⭐ {{ __('messages.badge_featured') }}</span></div>
        </div>
        <div class="article-card-body">
          <div class="article-cat"><i class="fa-solid fa-video" style="color:var(--gold)"></i> {{ $v->category?->name ?? __('messages.section_videos') }}</div>
          <div class="article-title">{{ Str::limit($v->title,70) }}</div>
          <div class="article-meta"><span><i class="fa-solid fa-eye"></i>{{ number_format($v->views) }}</span><span><i class="fa-solid fa-clock"></i>{{ $v->published_at?->diffForHumans() }}</span></div>
        </div>
      </a>
      @endforeach
    </div>
  </div>
  @endif

  <div class="section-header">
    <div class="section-title">{{ __('messages.section_all_videos') }}</div>
  </div>
  <div class="articles-grid-4">
    @forelse($videos as $v)
    <a href="{{ route('videos.show',$v->slug) }}" class="article-card">
      <div class="article-card-img" style="height:160px;background:#000">
        @if($v->thumbnail)
        <img src="{{ $v->thumbnail }}" alt="{{ $v->title }}" loading="lazy" onerror="this.style.display='none'">
        @endif
        <div style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center">
          <div style="width:40px;height:40px;background:rgba(200,154,43,.9);border-radius:50%;display:flex;align-items:center;justify-content:center"><i class="fa-solid fa-play" style="color:var(--black);font-size:14px;margin-right:-2px"></i></div>
        </div>
      </div>
      <div class="article-card-body" style="padding:12px">
        <div class="article-cat">{{ $v->category?->name }}</div>
        <div class="article-title" style="font-size:13px">{{ Str::limit($v->title,65) }}</div>
        <div class="article-meta"><span><i class="fa-solid fa-eye"></i>{{ number_format($v->views) }}</span></div>
      </div>
    </a>
    @empty
    <div style="grid-column:1/-1;text-align:center;padding:60px;color:rgba(255,255,255,.25)"><i class="fa-solid fa-video" style="font-size:48px;opacity:.3;display:block;margin-bottom:12px"></i><p>{{ __('messages.no_videos') }}</p></div>
    @endforelse
  </div>
  @if($videos->hasPages())<div style="margin-top:20px">{{ $videos->links() }}</div>@endif
</div>
</div>
@endsection
