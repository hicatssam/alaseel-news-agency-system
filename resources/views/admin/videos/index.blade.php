@extends('layouts.admin')
@section('title', __('admin.videos_index'))
@section('breadcrumb') {{ __('admin.nav_videos') }} @endsection
@section('content')
<div style="display:flex;justify-content:space-between;margin-bottom:20px">
  <div></div>
  <a href="{{ route('admin.videos.create') }}" class="btn btn-primary"><i class="fa-solid fa-plus"></i> {{ __('admin.videos_create') }}</a>
</div>
<div class="filter-bar">
  <form method="GET" style="display:flex;gap:10px;flex:1;flex-wrap:wrap">
    <input type="text" name="search" class="form-control" placeholder="{{ __('admin.placeholder_search') }}" value="{{ request('search') }}" style="max-width:280px">
    <select name="status" class="form-control" style="max-width:150px">
      <option value="">{{ __('admin.filter_all') }}</option>
      <option value="draft" {{ request('status')=='draft'?'selected':'' }}>{{ __('admin.status_draft') }}</option>
      <option value="published" {{ request('status')=='published'?'selected':'' }}>{{ __('admin.status_published') }}</option>
    </select>
    <select name="category_id" class="form-control" style="max-width:180px">
      <option value="">{{ __('admin.filter_all_categories') }}</option>
      @foreach($categories as $cat)
      <option value="{{ $cat->id }}" {{ request('category_id')==$cat->id?'selected':'' }}>{{ $cat->name }}</option>
      @endforeach
    </select>
    <button class="btn btn-secondary"><i class="fa-solid fa-search"></i></button>
  </form>
</div>
<div class="card">
  <div class="card-header"><span class="card-title">{{ __('admin.videos_index') }} ({{ $videos->total() }})</span></div>
  <div class="table-wrap">
    <table>
      <thead><tr><th>{{ __('admin.col_title') }}</th><th>{{ __('admin.col_category') }}</th><th>{{ __('admin.col_status') }}</th><th>{{ __('admin.col_featured') }}</th><th>{{ __('admin.col_views') }}</th><th>{{ __('admin.col_actions') }}</th></tr></thead>
      <tbody>
        @forelse($videos as $v)
        <tr>
          <td style="font-weight:600">{{ Str::limit($v->title,60) }}</td>
          <td><span class="badge badge-secondary">{{ $v->category?->name ?? '—' }}</span></td>
          <td>@if($v->status=='published')<span class="badge badge-success">{{ __('admin.status_published') }}</span>@else<span class="badge badge-secondary">{{ __('admin.status_draft') }}</span>@endif</td>
          <td>@if($v->is_featured)<span class="badge badge-gold">⭐</span>@else—@endif</td>
          <td style="font-weight:700;color:#c9a84c">{{ number_format($v->views) }}</td>
          <td>
            <div style="display:flex;gap:4px">
              <a href="{{ route('admin.videos.edit',$v) }}" class="btn btn-primary btn-sm btn-icon"><i class="fa-solid fa-pen"></i></a>
              <form method="POST" action="{{ route('admin.videos.destroy',$v) }}" onsubmit="return confirm('{{ __('admin.confirm_delete_video') }}')">
                @csrf @method('DELETE')
                <button class="btn btn-danger btn-sm btn-icon"><i class="fa-solid fa-trash"></i></button>
              </form>
            </div>
          </td>
        </tr>
        @empty
        <tr><td colspan="6"><div class="empty-state"><i class="fa-solid fa-video"></i><p>{{ __('admin.empty_videos') }}</p></div></td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  @if($videos->hasPages())<div style="padding:16px">{{ $videos->links() }}</div>@endif
</div>
@endsection
