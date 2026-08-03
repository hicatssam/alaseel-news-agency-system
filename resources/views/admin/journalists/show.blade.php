@extends('layouts.admin')
@section('title', __('admin.label_journalist_profile'))
@section('breadcrumb') <a href="{{ route('admin.journalists.index') }}">{{ __('admin.nav_journalists') }}</a> <span class="sep">›</span> {{ $journalist->name }} @endsection
@section('content')
<div style="display:grid;grid-template-columns:280px 1fr;gap:20px;align-items:start">
  <div class="card">
    <div class="card-body" style="text-align:center">
      <div style="width:80px;height:80px;border-radius:50%;background:rgba(201,168,76,.15);display:flex;align-items:center;justify-content:center;font-size:28px;font-weight:700;color:#c9a84c;margin:0 auto 12px">{{ mb_substr($journalist->name,0,1) }}</div>
      <div style="font-size:18px;font-weight:900;color:#1a1a2e;margin-bottom:4px">{{ $journalist->name }}</div>
      <div style="font-size:13px;color:#888;margin-bottom:16px">{{ $journalist->job_title ?? '' }}</div>
      @if($journalist->bio)<p style="font-size:13px;color:#555;line-height:1.7;text-align:right">{{ $journalist->bio }}</p>@endif
      <div style="display:flex;justify-content:center;gap:8px;margin-top:12px">
        @if($journalist->facebook)<a href="{{ $journalist->facebook }}" target="_blank" style="color:#1877f2;font-size:18px"><i class="fa-brands fa-facebook"></i></a>@endif
        @if($journalist->x_twitter)<a href="{{ $journalist->x_twitter }}" target="_blank" style="color:#000;font-size:18px"><i class="fa-brands fa-x-twitter"></i></a>@endif
        @if($journalist->instagram)<a href="{{ $journalist->instagram }}" target="_blank" style="color:#e1306c;font-size:18px"><i class="fa-brands fa-instagram"></i></a>@endif
      </div>
      <div style="margin-top:16px;display:flex;flex-direction:column;gap:8px">
        <a href="{{ route('admin.journalists.edit',$journalist) }}" class="btn btn-primary"><i class="fa-solid fa-pen"></i> {{ __('admin.btn_edit') }}</a>
      </div>
    </div>
  </div>
  <div class="card">
    <div class="card-header">
      <span class="card-title">{{ __('admin.label_journalist_articles') }} {{ $journalist->name }} ({{ $articles->total() }})</span>
    </div>
    <div class="table-wrap">
      <table>
        <thead><tr><th>{{ __('admin.col_title') }}</th><th>{{ __('admin.col_category') }}</th><th>{{ __('admin.col_status') }}</th><th>{{ __('admin.col_views') }}</th><th>{{ __('admin.col_date') }}</th></tr></thead>
        <tbody>
          @forelse($articles as $a)
          <tr>
            <td><a href="{{ route('admin.articles.edit',$a) }}" style="color:#1a1a2e;font-weight:600;font-size:13px;text-decoration:none">{{ Str::limit($a->title,60) }}</a></td>
            <td><span class="badge badge-secondary">{{ $a->category?->name ?? '—' }}</span></td>
            <td>@if($a->status=='published')<span class="badge badge-success">{{ __('admin.status_published') }}</span>@else<span class="badge badge-secondary">{{ $a->status }}</span>@endif</td>
            <td style="font-weight:700;color:#c9a84c">{{ number_format($a->views) }}</td>
            <td style="font-size:12px;color:#888">{{ $a->created_at->format('Y/m/d') }}</td>
          </tr>
          @empty
          <tr><td colspan="5"><div class="empty-state"><p>{{ __('admin.empty_articles') }}</p></div></td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    @if($articles->hasPages())<div style="padding:16px">{{ $articles->links() }}</div>@endif
  </div>
</div>
@endsection
