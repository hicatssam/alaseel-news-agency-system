@extends('layouts.admin')
@section('title', __('admin.label_article_details'))
@section('breadcrumb') <a href="{{ route('admin.articles.index') }}">{{ __('admin.nav_articles') }}</a> <span class="sep">›</span> {{ __('admin.label_article_details') }} @endsection
@section('content')

<div style="display:grid;grid-template-columns:1fr 300px;gap:20px;align-items:start">
  <div>
    <div class="card" style="margin-bottom:16px">
      <div class="card-body">
        <div style="display:flex;gap:6px;margin-bottom:12px;flex-wrap:wrap">
          @if($article->is_breaking)<span class="badge badge-danger">🔴 {{ __('admin.badge_breaking') }}</span>@endif
          @if($article->is_featured)<span class="badge badge-gold">⭐ {{ __('admin.badge_featured') }}</span>@endif
          @if($article->is_editor_pick)<span class="badge badge-info">✏️ {{ __('admin.badge_editor_pick') }}</span>@endif
        </div>
        <h1 style="font-size:20px;font-weight:900;color:#1a1a2e;line-height:1.5;margin-bottom:12px">{{ $article->title }}</h1>
        @if($article->summary)
        <p style="color:#555;font-size:14px;line-height:1.7;margin-bottom:16px;padding:12px;background:#f8f9fa;border-radius:8px;border-right:3px solid #c9a84c">{{ $article->summary }}</p>
        @endif
        @if($article->main_image)
        <img src="{{ $article->main_image }}" alt="{{ $article->title }}" style="width:100%;border-radius:10px;margin-bottom:16px;max-height:350px;object-fit:cover" onerror="this.style.display='none'">
        @endif
        <div style="font-size:14px;line-height:1.9;color:#333">{!! $article->content !!}</div>
      </div>
    </div>
    <div class="card" style="margin-bottom:16px">
      <div class="card-header"><span class="card-title">{{ __('admin.label_comments_section') }} ({{ $article->comments->count() }})</span></div>
      <div class="card-body" style="padding:0">
        @forelse($article->comments as $comment)
        <div style="padding:12px 16px;border-bottom:1px solid #f0f0f0">
          <div style="display:flex;justify-content:space-between;align-items:flex-start">
            <div style="font-weight:700;font-size:13px">{{ $comment->name ?? $comment->user?->name ?? __('admin.label_unknown_commenter') }}</div>
            @if($comment->status=='approved')<span class="badge badge-success">{{ __('admin.status_approved') }}</span>
            @elseif($comment->status=='pending')<span class="badge badge-warning">{{ __('admin.status_waiting') }}</span>
            @else<span class="badge badge-danger">{{ __('admin.status_rejected') }}</span>@endif
          </div>
          <p style="font-size:13px;color:#555;margin-top:6px">{{ $comment->content }}</p>
          <span style="font-size:11px;color:#999">{{ $comment->created_at->format('Y/m/d H:i') }}</span>
        </div>
        @empty
        <div class="empty-state"><p>{{ __('admin.empty_comments') }}</p></div>
        @endforelse
      </div>
    </div>
  </div>
  <div>
    <div class="card" style="margin-bottom:16px">
      <div class="card-header"><span class="card-title">{{ __('admin.label_article_info') }}</span></div>
      <div class="card-body" style="font-size:13.5px">
        <div style="margin-bottom:10px"><strong>{{ __('admin.col_status') }}:</strong>
          @php $statusLabels = ['draft' => __('admin.status_draft'), 'under_review' => __('admin.status_review'), 'approved' => __('admin.status_approved'), 'published' => __('admin.status_published'), 'scheduled' => __('admin.status_scheduled'), 'archived' => __('admin.status_archived'), 'rejected' => __('admin.status_rejected')] @endphp
          <span class="badge {{ $article->status=='published'?'badge-success':($article->status=='draft'?'badge-secondary':'badge-warning') }}">{{ $statusLabels[$article->status]??$article->status }}</span>
        </div>
        <div style="margin-bottom:10px"><strong>{{ __('admin.label_category') }}:</strong> {{ $article->category?->name ?? '—' }}</div>
        <div style="margin-bottom:10px"><strong>{{ __('admin.label_journalist') }}:</strong> {{ $article->journalist?->name ?? '—' }}</div>
        <div style="margin-bottom:10px"><strong>{{ __('admin.col_views') }}:</strong> <span style="color:#c9a84c;font-weight:700">{{ number_format($article->views) }}</span></div>
        <div style="margin-bottom:10px"><strong>{{ __('admin.label_reading_time') }}:</strong> {{ $article->reading_time }} {{ __('admin.label_minutes_short') }}</div>
        <div style="margin-bottom:10px"><strong>{{ __('admin.label_created_at') }}:</strong> {{ $article->created_at->format('Y/m/d H:i') }}</div>
        @if($article->published_at)
        <div style="margin-bottom:10px"><strong>{{ __('admin.label_published_at') }}:</strong> {{ $article->published_at->format('Y/m/d H:i') }}</div>
        @endif
        @if($article->tags->count())
        <div><strong>{{ __('admin.label_tags') }}:</strong>
          <div style="display:flex;flex-wrap:wrap;gap:4px;margin-top:6px">
            @foreach($article->tags as $tag)
            <span class="badge badge-secondary">{{ $tag->name }}</span>
            @endforeach
          </div>
        </div>
        @endif
      </div>
    </div>
    <div class="card" style="margin-bottom:16px">
      <div class="card-header"><span class="card-title">{{ __('admin.label_actions_section') }}</span></div>
      <div class="card-body" style="display:flex;flex-direction:column;gap:8px">
        <a href="{{ route('admin.articles.edit',$article) }}" class="btn btn-primary"><i class="fa-solid fa-pen"></i> {{ __('admin.btn_edit_article') }}</a>
        <a href="{{ route('admin.articles.revisions',$article) }}" class="btn btn-outline"><i class="fa-solid fa-clock-rotate-left"></i> {{ __('admin.label_revisions_section') }} ({{ $article->revisions->count() }})</a>
        <a href="{{ route('articles.show',$article->slug) }}" target="_blank" class="btn btn-outline"><i class="fa-solid fa-eye"></i> {{ __('admin.btn_view_on_site') }}</a>
        <form method="POST" action="{{ route('admin.articles.destroy',$article) }}" onsubmit="return confirm('{{ __('admin.confirm_delete_article_perm') }}')">
          @csrf @method('DELETE')
          <button type="submit" class="btn btn-danger" style="width:100%"><i class="fa-solid fa-trash"></i> {{ __('admin.btn_delete_article') }}</button>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
