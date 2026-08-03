@extends('layouts.admin')
@section('title', __('admin.btn_revision_log'))
@section('breadcrumb') <a href="{{ route('admin.articles.index') }}">{{ __('admin.nav_articles') }}</a> <span class="sep">›</span> <a href="{{ route('admin.articles.show',$article) }}">{{ Str::limit($article->title,40) }}</a> <span class="sep">›</span> {{ __('admin.label_revisions_section') }} @endsection
@section('content')

<div class="card">
  <div class="card-header">
    <span class="card-title">{{ __('admin.label_revision_log_title') }}: {{ Str::limit($article->title,60) }}</span>
    <a href="{{ route('admin.articles.edit',$article) }}" class="btn btn-primary btn-sm"><i class="fa-solid fa-pen"></i> {{ __('admin.btn_edit') }}</a>
  </div>
  <div class="card-body" style="padding:0">
    @forelse($revisions as $rev)
    <div style="padding:20px;border-bottom:1px solid #f0f0f0">
      <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:12px">
        <div>
          <span style="font-weight:700;font-size:13px">{{ $rev->editor?->name ?? __('admin.label_system') }}</span>
          <span style="font-size:12px;color:#888;margin-right:8px">{{ $rev->created_at->format('Y/m/d H:i') }}</span>
          <span style="font-size:11px;color:#aaa">({{ $rev->created_at->diffForHumans() }})</span>
        </div>
        @if($rev->old_status)
        <span class="badge badge-secondary">{{ $rev->old_status }}</span>
        @endif
      </div>
      @if($rev->revision_note)
      <div style="padding:8px 12px;background:#fff3cd;border-radius:6px;font-size:13px;margin-bottom:10px;color:#856404">
        <i class="fa-solid fa-note-sticky"></i> {{ $rev->revision_note }}
      </div>
      @endif
      @if($rev->old_title)
      <div style="margin-bottom:8px">
        <div style="font-size:12px;color:#888;margin-bottom:3px">{{ __('admin.label_prev_title') }}:</div>
        <div style="font-size:13px;color:#555;background:#f8f9fa;padding:8px;border-radius:6px">{{ $rev->old_title }}</div>
      </div>
      @endif
      @if($rev->old_summary)
      <div>
        <div style="font-size:12px;color:#888;margin-bottom:3px">{{ __('admin.label_prev_summary') }}:</div>
        <div style="font-size:13px;color:#555;background:#f8f9fa;padding:8px;border-radius:6px">{{ Str::limit($rev->old_summary,200) }}</div>
      </div>
      @endif
    </div>
    @empty
    <div class="empty-state" style="padding:60px"><i class="fa-solid fa-clock-rotate-left"></i><p>{{ __('admin.empty_revisions') }}</p></div>
    @endforelse
  </div>
  @if($revisions->hasPages())
  <div style="padding:16px">{{ $revisions->links() }}</div>
  @endif
</div>
@endsection
