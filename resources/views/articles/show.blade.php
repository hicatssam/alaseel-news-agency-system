@extends('layouts.app')
@section('title', $article->seo_title ?? $article->title)
@section('description', $article->seo_description ?? $article->summary)
@section('content')
@php $isRtl = app()->getLocale() === 'ar'; @endphp

<div class="main-content">
<div class="container">
  <div style="display:grid;grid-template-columns:1fr 300px;gap:28px;align-items:start">
    <main>
      <div style="background:var(--surface);border:1px solid var(--border);border-radius:12px;padding:28px">
        <div style="margin-bottom:16px;display:flex;gap:8px;flex-wrap:wrap;align-items:center">
          @if($article->category)
          <a href="{{ route('categories.show',$article->category->slug) }}" style="background:rgba(200,154,43,.12);color:var(--gold);padding:4px 12px;border-radius:20px;font-size:12px;font-weight:700">
            {{ $article->category->name }}
          </a>
          @endif
          @if($article->is_breaking)<span class="badge-breaking">{{ __('messages.breaking') }}</span>@endif
          @if($article->is_featured)<span class="badge-featured">⭐ {{ __('messages.badge_featured') }}</span>@endif
          @if($article->is_editor_pick)<span style="background:rgba(13,202,240,.15);color:#0dcaf0;font-size:10px;font-weight:700;padding:3px 7px;border-radius:4px">✏️ {{ __('messages.badge_editor_pick') }}</span>@endif
        </div>

        <h1 style="font-size:24px;font-weight:900;color:var(--white);line-height:1.5;margin-bottom:14px">{{ $article->title }}</h1>

        @if($article->summary)
        <p style="font-size:15px;color:rgba(255,255,255,.6);line-height:1.8;padding:14px;background:var(--surface2);border-radius:8px;border-{{ $isRtl ? 'right' : 'left' }}:3px solid var(--gold);margin-bottom:20px">{{ $article->summary }}</p>
        @endif

        <div style="display:flex;align-items:center;gap:16px;margin-bottom:20px;padding-bottom:16px;border-bottom:1px solid var(--border);flex-wrap:wrap">
          @if($article->journalist)
          <div style="display:flex;align-items:center;gap:8px">
            <div style="width:36px;height:36px;border-radius:50%;background:rgba(200,154,43,.15);display:flex;align-items:center;justify-content:center;font-weight:700;color:var(--gold);font-size:14px">{{ mb_substr($article->journalist->name,0,1) }}</div>
            <div>
              <div style="font-size:13px;font-weight:700;color:var(--white)">{{ $article->journalist->name }}</div>
              <div style="font-size:11px;color:rgba(255,255,255,.4)">{{ $article->journalist->job_title }}</div>
            </div>
          </div>
          @endif
          <div style="display:flex;gap:12px;font-size:12.5px;color:rgba(255,255,255,.4);flex-wrap:wrap">
            <span><i class="fa-solid fa-calendar" style="color:var(--gold)"></i> {{ $article->published_at?->format('Y/m/d H:i') }}</span>
            <span><i class="fa-solid fa-eye" style="color:var(--gold)"></i> {{ __('messages.views_count',['count'=>number_format($article->views)]) }}</span>
            <span><i class="fa-solid fa-clock" style="color:var(--gold)"></i> {{ __('messages.reading_time',['mins'=>$article->reading_time]) }}</span>
          </div>
        </div>

        @if($article->main_image)
        <figure style="margin-bottom:24px">
          <img src="{{ $article->main_image }}" alt="{{ $article->title }}" style="width:100%;border-radius:10px;max-height:450px;object-fit:cover" onerror="this.style.display='none'">
        </figure>
        @endif

        <div style="font-size:15.5px;line-height:2;color:var(--text);margin-bottom:28px">
          {!! $article->content !!}
        </div>

        @if($article->tags->count())
        <div style="margin-bottom:24px;padding-top:16px;border-top:1px solid var(--border)">
          <div style="font-weight:700;font-size:13px;color:rgba(255,255,255,.5);margin-bottom:8px"><i class="fa-solid fa-tags" style="color:var(--gold)"></i> {{ __('messages.tags_label') }}</div>
          <div class="tags-cloud">
            @foreach($article->tags as $tag)
            <a href="{{ route('search',['q'=>$tag->name]) }}" class="tag-chip">{{ $tag->name }}</a>
            @endforeach
          </div>
        </div>
        @endif

        {{-- Share --}}
        <div style="background:var(--surface2);border-radius:10px;padding:16px;margin-bottom:24px">
          <div style="font-size:13px;font-weight:700;color:rgba(255,255,255,.6);margin-bottom:10px">{{ __('messages.share_article') }}</div>
          <div style="display:flex;gap:8px;flex-wrap:wrap">
            <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->url()) }}" target="_blank" style="background:#1877f2;color:#fff;padding:8px 16px;border-radius:6px;font-size:13px;display:flex;align-items:center;gap:6px;font-weight:600"><i class="fa-brands fa-facebook-f"></i> Facebook</a>
            <a href="https://twitter.com/intent/tweet?text={{ urlencode($article->title) }}&url={{ urlencode(request()->url()) }}" target="_blank" style="background:#000;color:#fff;padding:8px 16px;border-radius:6px;font-size:13px;display:flex;align-items:center;gap:6px;font-weight:600"><i class="fa-brands fa-x-twitter"></i> X</a>
            <a href="https://wa.me/?text={{ urlencode($article->title.' '.request()->url()) }}" target="_blank" style="background:#25d366;color:#fff;padding:8px 16px;border-radius:6px;font-size:13px;display:flex;align-items:center;gap:6px;font-weight:600"><i class="fa-brands fa-whatsapp"></i> WhatsApp</a>
          </div>
        </div>

        {{-- Comments --}}
        <div>
          <div style="font-size:16px;font-weight:900;color:var(--white);margin-bottom:16px;padding-bottom:8px;border-bottom:2px solid var(--gold)">
            {{ __('messages.comments_title',['count'=>$article->approvedComments->count()]) }}
          </div>
          @foreach($article->approvedComments as $comment)
          <div style="background:var(--surface2);border-radius:8px;padding:14px;margin-bottom:12px">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px">
              <span style="font-weight:700;font-size:13px;color:var(--white)">{{ $comment->name ?? __('messages.comment_anonymous') }}</span>
              <span style="font-size:11px;color:rgba(255,255,255,.3)">{{ $comment->created_at->diffForHumans() }}</span>
            </div>
            <p style="font-size:13.5px;color:rgba(255,255,255,.6);line-height:1.7">{{ $comment->content }}</p>
          </div>
          @endforeach

          <form action="{{ route('articles.comment',$article) }}" method="POST" style="margin-top:20px;background:var(--surface2);border-radius:10px;padding:20px">
            @csrf
            <div style="font-size:14px;font-weight:700;margin-bottom:14px;color:var(--white)"><i class="fa-solid fa-comment-dots" style="color:var(--gold)"></i> {{ __('messages.comment_add') }}</div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:12px">
              <div>
                <label style="font-size:12px;font-weight:600;color:rgba(255,255,255,.5);display:block;margin-bottom:4px">{{ __('messages.comment_name') }}</label>
                <input type="text" name="name" required style="width:100%;padding:10px;background:var(--surface);border:1px solid var(--border);border-radius:8px;font-family:'Cairo',sans-serif;color:var(--white)" placeholder="{{ __('messages.comment_name') }}">
              </div>
              <div>
                <label style="font-size:12px;font-weight:600;color:rgba(255,255,255,.5);display:block;margin-bottom:4px">{{ __('messages.comment_email') }}</label>
                <input type="email" name="email" style="width:100%;padding:10px;background:var(--surface);border:1px solid var(--border);border-radius:8px;font-family:'Cairo',sans-serif;color:var(--white)">
              </div>
            </div>
            <div style="margin-bottom:12px">
              <label style="font-size:12px;font-weight:600;color:rgba(255,255,255,.5);display:block;margin-bottom:4px">{{ __('messages.comment_content') }}</label>
              <textarea name="content" required rows="4" style="width:100%;padding:10px;background:var(--surface);border:1px solid var(--border);border-radius:8px;font-family:'Cairo',sans-serif;resize:vertical;color:var(--white)" placeholder="{{ __('messages.comment_placeholder') }}"></textarea>
            </div>
            <button type="submit" class="btn btn-primary"><i class="fa-solid fa-paper-plane"></i> {{ __('messages.comment_submit') }}</button>
          </form>
        </div>
      </div>
    </main>

    {{-- Sidebar --}}
    <aside>
      <div class="sidebar-widget">
        <div class="widget-header"><span class="widget-title"><i class="fa-solid fa-newspaper"></i> {{ __('messages.related_news') }}</span></div>
        <div class="widget-body">
          @foreach($related as $r)
          <a href="{{ route('articles.show',$r->slug) }}" class="widget-article">
            <div style="width:68px;height:60px;border-radius:6px;background:var(--surface2);flex-shrink:0;overflow:hidden">
              @if($r->main_image)
              <img src="{{ $r->main_image }}" class="widget-article-img" onerror="this.style.display='none'">
              @endif
            </div>
            <div class="widget-article-body">
              <div class="widget-article-title">{{ Str::limit($r->title,65) }}</div>
              <div class="widget-article-meta">{{ $r->published_at?->diffForHumans() }}</div>
            </div>
          </a>
          @endforeach
        </div>
      </div>

      <div class="sidebar-widget">
        <div class="widget-header"><span class="widget-title"><i class="fa-solid fa-fire"></i> {{ __('messages.most_read') }}</span></div>
        <div class="widget-body">
          @foreach($popular as $i => $p)
          <a href="{{ route('articles.show',$p->slug) }}" class="widget-article">
            <div style="font-size:22px;font-weight:900;color:{{ $i<3?'var(--gold)':'rgba(255,255,255,.2)' }};width:30px;flex-shrink:0;text-align:center">{{ $i+1 }}</div>
            <div class="widget-article-body">
              <div class="widget-article-title">{{ Str::limit($p->title,65) }}</div>
              <div class="widget-article-meta">{{ __('messages.views_count',['count'=>number_format($p->views)]) }}</div>
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
