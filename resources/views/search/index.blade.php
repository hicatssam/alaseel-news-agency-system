@extends('layouts.app')
@section('title',__('messages.search_page_title',['q'=>$q]))
@section('content')
<div class="main-content">
<div class="container">
  <div style="background:var(--surface);border:1px solid var(--border);border-radius:12px;padding:24px;margin-bottom:24px">
    <form action="{{ route('search') }}" method="GET" style="display:flex;gap:0;max-width:600px">
      <input type="text" name="q" class="search-input" value="{{ $q }}" placeholder="{{ __('messages.search_placeholder') }}" style="flex:1;padding:12px 16px;border-radius:8px 0 0 8px;border:1.5px solid var(--border)">
      <button type="submit" class="search-btn" style="padding:12px 20px;border-radius:0 8px 8px 0"><i class="fa-solid fa-search"></i> {{ __('messages.search_btn') }}</button>
    </form>
    @if($q)
    <p style="margin-top:12px;font-size:13.5px;color:rgba(255,255,255,.5)">{!! __('messages.search_results',['total'=>'<strong style="color:var(--gold)">'.$total.'</strong>','q'=>'<strong>'.$q.'</strong>']) !!}</p>
    @endif
  </div>

  @if($articles->count())
  <div class="section-header"><div class="section-title"><i class="fa-solid fa-newspaper" style="color:var(--gold)"></i> {{ __('messages.search_articles',['count'=>$articles->total()]) }}</div></div>
  <div class="articles-grid" style="margin-bottom:28px">
    @foreach($articles as $a)
    <div class="article-card">
      <div class="article-card-img">
        @if($a->main_image)
        <img src="{{ $a->main_image }}" alt="{{ $a->title }}" loading="lazy" onerror="this.style.display='none'">
        @else
        <div style="height:100%;display:flex;align-items:center;justify-content:center;background:var(--surface2)"><i class="fa-solid fa-newspaper" style="font-size:36px;color:rgba(200,154,43,.15)"></i></div>
        @endif
      </div>
      <div class="article-card-body">
        <div class="article-cat">{{ $a->category?->name }}</div>
        <div class="article-title"><a href="{{ route('articles.show',$a->slug) }}">{{ Str::limit($a->title,80) }}</a></div>
        <div class="article-meta">
          <span><i class="fa-solid fa-clock"></i>{{ $a->published_at?->diffForHumans() }}</span>
          <span><i class="fa-solid fa-eye"></i>{{ number_format($a->views) }}</span>
        </div>
      </div>
    </div>
    @endforeach
  </div>
  @if($articles->hasPages()){{ $articles->links() }}@endif
  @endif

  @if($videos->count())
  <div class="section-header" style="margin-top:28px"><div class="section-title"><i class="fa-solid fa-video" style="color:var(--gold)"></i> {{ __('messages.search_videos_count',['count'=>$videos->count()]) }}</div></div>
  <div class="articles-grid-4" style="margin-bottom:28px">
    @foreach($videos as $v)
    <a href="{{ route('videos.show',$v->slug) }}" class="article-card">
      <div class="article-card-img" style="height:140px;background:var(--surface2)">
        <div style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center"><i class="fa-solid fa-play-circle" style="font-size:36px;color:rgba(200,154,43,.6)"></i></div>
      </div>
      <div class="article-card-body" style="padding:12px">
        <div class="article-title" style="font-size:13px">{{ Str::limit($v->title,60) }}</div>
      </div>
    </a>
    @endforeach
  </div>
  @endif

  @if(!$q)
  <div style="text-align:center;padding:60px;color:rgba(255,255,255,.25)">
    <i class="fa-solid fa-search" style="font-size:48px;margin-bottom:12px;display:block;opacity:.3"></i>
    <p>{{ __('messages.search_enter_keyword') }}</p>
  </div>
  @elseif($total === 0)
  <div style="text-align:center;padding:60px;color:rgba(255,255,255,.25)">
    <i class="fa-solid fa-magnifying-glass" style="font-size:48px;margin-bottom:12px;display:block;opacity:.3"></i>
    <p style="font-size:16px">{{ __('messages.search_no_results',['q'=>$q]) }}</p>
    <p style="font-size:13px;margin-top:8px">{{ __('messages.search_try_different') }}</p>
  </div>
  @endif
</div>
</div>
@endsection
