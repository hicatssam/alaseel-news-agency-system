@extends('layouts.app')
@section('title', $article->seo_title ?? $article->title)
@section('description', $article->seo_description ?? $article->summary)
@section('content')
@php
  $isRtl = app()->getLocale() === 'ar';

  $mediaUrl = function ($path) {
      if (blank($path)) {
          return null;
      }

      $path = str_replace('\\', '/', trim($path));

      if (\Illuminate\Support\Str::startsWith($path, ['http://', 'https://', '//'])) {
          return $path;
      }

      // Convert absolute/local storage paths to paths relative to storage/app/public.
      $path = preg_replace('#^.*storage/app/public/#', '', $path);
      $path = preg_replace('#^(?:public/|/?storage/)#', '', $path);

      return asset('storage/' . ltrim($path, '/'));
  };

  // Supports both the media-library relation and the legacy main_image field.
  $articleImage = $mediaUrl(
      $article->main_image_url
      ?? $article->main_image
      ?? null
  );
  $articleVideo = $mediaUrl($article->video_url ?? $article->video ?? null);
  $articleShareUrl = route('articles.show', $article->slug);
  $videoEmbed = null;

  if ($articleVideo) {
      if (preg_match('~(?:youtube\.com/(?:watch\?v=|embed/|shorts/)|youtu\.be/)([A-Za-z0-9_-]{6,})~', $articleVideo, $match)) {
          $videoEmbed = 'https://www.youtube.com/embed/' . $match[1];
      } elseif (preg_match('~vimeo\.com/(?:video/)?([0-9]+)~', $articleVideo, $match)) {
          $videoEmbed = 'https://player.vimeo.com/video/' . $match[1];
      }
  }
@endphp

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

        {{-- @if($article->summary)
        <p style="font-size:15px;color:rgba(255,255,255,.6);line-height:1.8;padding:14px;background:var(--surface2);border-radius:8px;border-{{ $isRtl ? 'right' : 'left' }}:3px solid var(--gold);margin-bottom:20px">{{ $article->summary }}</p>
        @endif --}}

        <div style="display:flex;align-items:center;justify-content:space-between;gap:16px;margin-bottom:20px;padding-bottom:16px;border-bottom:1px solid var(--border);flex-wrap:wrap">
          @if($article->journalist)
            @php
              $journalist = $article->journalist;
              $journalistImage = null;

              if (!empty($journalist->photo)) {
                  $journalistImage = $mediaUrl($journalist->photo);
              }
            @endphp

            <div style="display:flex;align-items:center;gap:10px">
              <div style="width:44px;height:44px;border-radius:50%;overflow:hidden;flex-shrink:0;background:rgba(200,154,43,.15);border:1px solid rgba(200,154,43,.35)">
                @if($journalistImage)
                  <img
                    src="{{ $journalistImage }}"
                    alt="{{ $journalist->name }}"
                    style="display:block;width:100%;height:100%;object-fit:cover"
                    onerror="this.style.display='none';this.nextElementSibling.style.display='flex'"
                  >
                  <div style="display:none;width:100%;height:100%;align-items:center;justify-content:center;color:var(--gold);font-size:16px;font-weight:800">
                    {{ mb_substr($journalist->name, 0, 1) }}
                  </div>
                @else
                  <div style="display:flex;width:100%;height:100%;align-items:center;justify-content:center;color:var(--gold);font-size:16px;font-weight:800">
                    {{ mb_substr($journalist->name, 0, 1) }}
                  </div>
                @endif
              </div>

              <div>
                <div style="font-size:13px;font-weight:800;color:var(--white)">{{ $journalist->name }}</div>
                @if($journalist->job_title)
                  <div style="margin-top:2px;font-size:11px;color:rgba(255,255,255,.4)">{{ $journalist->job_title }}</div>
                @endif
              </div>
            </div>
          @endif

          <div style="display:flex;gap:12px;font-size:12.5px;color:rgba(255,255,255,.4);flex-wrap:wrap">
            <span><i class="fa-solid fa-calendar" style="color:var(--gold)"></i> {{ $article->published_at?->format('Y/m/d H:i') }}</span>
            {{-- <span><i class="fa-solid fa-eye" style="color:var(--gold)"></i> {{ __('messages.views_count',['count'=>number_format($article->views)]) }}</span> --}}
            <span><i class="fa-solid fa-clock" style="color:var(--gold)"></i> {{ __('messages.reading_time',['mins'=>$article->reading_time]) }}</span>
          </div>
        </div>

        {{-- Main article media: video has priority, otherwise show the image. --}}
        @if($articleVideo)
          <div class="article-main-media">
            @if($videoEmbed)
              <iframe
                src="{{ $videoEmbed }}"
                title="{{ $article->title }}"
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                allowfullscreen
              ></iframe>
            @else
              <video controls playsinline preload="metadata" @if($articleImage) poster="{{ $articleImage }}" @endif>
                <source src="{{ $articleVideo }}">
                متصفحك لا يدعم تشغيل الفيديو.
              </video>
            @endif
          </div>
        @elseif($articleImage)
          <figure class="article-main-media">
            <img src="{{ $articleImage }}" alt="{{ $article->mainImageMedia?->alt_text ?: $article->title }}" onerror="this.closest('.article-main-media').style.display='none'">
            @if($article->mainImageMedia?->caption)
              <figcaption class="article-main-caption">{{ $article->mainImageMedia->caption }}</figcaption>
            @endif
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

        {{-- Related News --}}
        @if(isset($related) && $related->isNotEmpty())
        <section class="article-related-section">
          <div class="article-related-heading">
            <i class="fa-solid fa-newspaper"></i>
            {{ __('messages.related_news') }}
          </div>

          <div class="article-related-grid">
            @foreach($related->take(4) as $relatedArticle)
            <a href="{{ route('articles.show', $relatedArticle->slug) }}" class="article-related-card">
              <div class="article-related-image">
                @php
                  $relatedImage = $mediaUrl(
                      $relatedArticle->main_image_url
                      ?? $relatedArticle->main_image
                      ?? null
                  );
                @endphp
                @if($relatedImage)
                  <img
                    src="{{ $relatedImage }}"
                    alt="{{ $relatedArticle->title }}"
                    loading="lazy"
                    onerror="this.style.display='none';this.nextElementSibling.style.display='flex'"
                  >
                  <div class="article-related-placeholder"><i class="fa-regular fa-image"></i></div>
                @else
                  <div class="article-related-placeholder" style="display:flex"><i class="fa-regular fa-image"></i></div>
                @endif

                @if($relatedArticle->category)
                  <span class="article-related-category">{{ $relatedArticle->category->name }}</span>
                @endif
              </div>

              <div class="article-related-content">
                <h3>{{ \Illuminate\Support\Str::limit($relatedArticle->title, 85) }}</h3>
                <div class="article-related-meta">
                  <span><i class="fa-regular fa-calendar"></i> {{ $relatedArticle->published_at?->format('Y/m/d') }}</span>
                  <span><i class="fa-regular fa-eye"></i> {{ number_format($relatedArticle->views ?? 0) }}</span>
                </div>
              </div>
            </a>
            @endforeach
          </div>
        </section>
        @endif

        {{-- Article permalink and sharing --}}
        <div class="article-share-box">
          <div class="article-share-title">
            <i class="fa-solid fa-share-nodes"></i>
            {{ __('messages.share_article') }}
          </div>

          <div class="article-share-link-row">
            <input id="article-share-url" type="text" value="{{ $articleShareUrl }}" readonly aria-label="رابط المقال">
            <button id="copy-article-link" type="button" class="article-copy-button">
              <i class="fa-regular fa-copy"></i>
              <span>نسخ الرابط</span>
            </button>
          </div>

          <div id="copy-article-feedback" class="article-copy-feedback" role="status" aria-live="polite"></div>

          <div class="article-share-buttons">
            <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode($articleShareUrl) }}" target="_blank" rel="noopener noreferrer" class="share-facebook"><i class="fa-brands fa-facebook-f"></i> Facebook</a>
            <a href="https://twitter.com/intent/tweet?text={{ urlencode($article->title) }}&url={{ urlencode($articleShareUrl) }}" target="_blank" rel="noopener noreferrer" class="share-x"><i class="fa-brands fa-x-twitter"></i> X</a>
            <a href="https://wa.me/?text={{ urlencode($article->title . ' ' . $articleShareUrl) }}" target="_blank" rel="noopener noreferrer" class="share-whatsapp"><i class="fa-brands fa-whatsapp"></i> WhatsApp</a>
            <a href="https://t.me/share/url?url={{ urlencode($articleShareUrl) }}&text={{ urlencode($article->title) }}" target="_blank" rel="noopener noreferrer" class="share-telegram"><i class="fa-brands fa-telegram"></i> Telegram</a>
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
              @php
                $sidebarImage = $mediaUrl(
                    $r->main_image_url
                    ?? $r->main_image
                    ?? null
                );
              @endphp
              @if($sidebarImage)
              <img src="{{ $sidebarImage }}" class="widget-article-img" alt="{{ $r->title }}" onerror="this.style.display='none'">
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

<style>
  .article-main-media{position:relative;width:100%;margin:0 0 24px;overflow:hidden;border-radius:10px;background:#000;aspect-ratio:16/9}
  .article-main-media img,.article-main-media video,.article-main-media iframe{display:block;width:100%;height:100%;border:0;object-fit:cover}
  .article-main-caption{position:absolute;inset-inline:0;bottom:0;padding:9px 12px;color:#fff;background:rgba(0,0,0,.65);font-size:12px}
  .article-related-section{margin-bottom:28px;padding-top:20px;border-top:1px solid var(--border)}
  .article-related-heading{margin-bottom:16px;padding-bottom:9px;border-bottom:2px solid var(--gold);color:var(--white);font-size:16px;font-weight:900}
  .article-related-heading i{margin-inline-end:6px;color:var(--gold)}
  .article-related-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:14px}
  .article-related-card{display:block;overflow:hidden;color:inherit;text-decoration:none;background:var(--surface2);border:1px solid var(--border);border-radius:10px;transition:transform .2s ease,border-color .2s ease}
  .article-related-card:hover{border-color:var(--gold);transform:translateY(-3px)}
  .article-related-image{position:relative;height:145px;overflow:hidden;background:var(--surface)}
  .article-related-image img{display:block;width:100%;height:100%;object-fit:cover;transition:transform .3s ease}
  .article-related-card:hover .article-related-image img{transform:scale(1.04)}
  .article-related-placeholder{display:none;width:100%;height:100%;align-items:center;justify-content:center;color:rgba(255,255,255,.2);font-size:30px}
  .article-related-category{position:absolute;inset-inline-start:9px;bottom:9px;padding:3px 9px;color:#111;background:var(--gold);border-radius:15px;font-size:10px;font-weight:800}
  .article-related-content{padding:12px}
  .article-related-content h3{min-height:44px;margin:0 0 10px;color:var(--white);font-size:13px;font-weight:800;line-height:1.7}
  .article-related-meta{display:flex;justify-content:space-between;gap:8px;color:rgba(255,255,255,.4);font-size:10.5px}
  .article-related-meta i{margin-inline-end:3px;color:var(--gold)}
  .article-share-box{margin-bottom:24px;padding:16px;background:var(--surface2);border:1px solid var(--border);border-radius:10px}
  .article-share-title{display:flex;align-items:center;gap:7px;margin-bottom:12px;color:rgba(255,255,255,.7);font-size:13px;font-weight:800}
  .article-share-title i{color:var(--gold)}
  .article-share-link-row{display:flex;gap:8px;margin-bottom:10px}
  .article-share-link-row input{min-width:0;flex:1;padding:10px 12px;color:rgba(255,255,255,.75);background:var(--surface);border:1px solid var(--border);border-radius:7px;font-family:'Cairo',sans-serif;font-size:12px;direction:ltr;text-align:left}
  .article-copy-button,.article-share-buttons a{display:inline-flex;align-items:center;justify-content:center;gap:6px;border:0;border-radius:6px;color:#fff;font-family:'Cairo',sans-serif;font-size:13px;font-weight:700;cursor:pointer;text-decoration:none}
  .article-copy-button{padding:8px 14px;background:var(--gold);color:#111;white-space:nowrap}
  .article-copy-feedback{display:none;margin:-2px 0 10px;color:#4ade80;font-size:11px;font-weight:700}
  .article-share-buttons{display:flex;gap:8px;flex-wrap:wrap}
  .article-share-buttons a{padding:8px 14px}
  .share-facebook{background:#1877f2}
  .share-x{background:#000}
  .share-whatsapp{background:#25d366}
  .share-telegram{background:#229ed9}

  @media (max-width:900px){
    .container>div[style\\\*="grid-template-columns:1fr 300px"]{grid-template-columns:1fr!important}
  }

  @media (max-width:650px){
    .article-related-grid{grid-template-columns:1fr}
    .article-related-image{height:190px}
    .article-share-link-row{flex-direction:column}
    .article-copy-button{width:100%}
    .article-share-buttons a{flex:1;min-width:120px}
  }
</style>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    const copyButton = document.getElementById('copy-article-link');
    const shareInput = document.getElementById('article-share-url');
    const feedback = document.getElementById('copy-article-feedback');

    if (!copyButton || !shareInput || !feedback) return;

    copyButton.addEventListener('click', async function () {
      try {
        if (navigator.clipboard && window.isSecureContext) {
          await navigator.clipboard.writeText(shareInput.value);
        } else {
          shareInput.focus();
          shareInput.select();
          document.execCommand('copy');
          window.getSelection()?.removeAllRanges();
        }

        feedback.textContent = 'تم نسخ رابط المقال بنجاح';
        feedback.style.display = 'block';
        copyButton.querySelector('span').textContent = 'تم النسخ';

        window.setTimeout(function () {
          feedback.style.display = 'none';
          copyButton.querySelector('span').textContent = 'نسخ الرابط';
        }, 2500);
      } catch (error) {
        shareInput.focus();
        shareInput.select();
        feedback.textContent = 'حدد الرابط وانسخه يدويًا';
        feedback.style.display = 'block';
      }
    });
  });
</script>
@endsection