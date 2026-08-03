@extends('layouts.app')
@section('title', $video->title)
@section('content')
<div class="main-content">
<div class="container">
  <div style="display:grid;grid-template-columns:1fr 300px;gap:28px;align-items:start">
    <div>
      <div style="background:var(--surface);border:1px solid var(--border);border-radius:12px;overflow:hidden">
        @if($video->embed_url)
        <div style="position:relative;padding-bottom:56.25%;height:0;background:#000">
          <iframe src="{{ $video->embed_url }}" style="position:absolute;top:0;left:0;width:100%;height:100%;border:none" allowfullscreen></iframe>
        </div>
        @else
        <div style="height:400px;background:var(--surface2);display:flex;align-items:center;justify-content:center">
          <i class="fa-solid fa-play-circle" style="font-size:80px;color:rgba(200,154,43,.3)"></i>
        </div>
        @endif
        <div style="padding:24px">
          <div class="article-cat" style="margin-bottom:8px">{{ $video->category?->name }}</div>
          <h1 style="font-size:20px;font-weight:900;color:var(--white);line-height:1.5;margin-bottom:12px">{{ $video->title }}</h1>
          @if($video->description)
          <p style="font-size:14px;color:rgba(255,255,255,.5);line-height:1.8">{{ $video->description }}</p>
          @endif
          <div class="article-meta" style="margin-top:12px">
            <span><i class="fa-solid fa-eye"></i>{{ __('messages.views_count',['count'=>number_format($video->views)]) }}</span>
            <span><i class="fa-solid fa-clock"></i>{{ $video->published_at?->diffForHumans() }}</span>
          </div>
        </div>
      </div>
    </div>
    <aside>
      <div class="sidebar-widget">
        <div class="widget-header"><span class="widget-title"><i class="fa-solid fa-video"></i> {{ __('messages.section_other_videos') }}</span></div>
        <div class="widget-body">
          @foreach($related as $r)
          <a href="{{ route('videos.show',$r->slug) }}" class="widget-article">
            <div style="width:68px;height:60px;background:var(--surface2);border-radius:6px;flex-shrink:0;overflow:hidden;position:relative">
              @if($r->thumbnail)
              <img src="{{ $r->thumbnail }}" class="widget-article-img" style="border-radius:6px" onerror="this.style.display='none'">
              @endif
              <div style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center">
                <i class="fa-solid fa-play" style="font-size:14px;color:rgba(200,154,43,.7)"></i>
              </div>
            </div>
            <div class="widget-article-body">
              <div class="widget-article-title">{{ Str::limit($r->title,65) }}</div>
              <div class="widget-article-meta">{{ __('messages.views_count',['count'=>number_format($r->views)]) }}</div>
            </div>
          </a>
          @endforeach
        </div>
      </div>
    </aside>
  </div>
</div>
</div>
@endsection
