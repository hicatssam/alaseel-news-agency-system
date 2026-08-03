@extends('layouts.admin')
@section('title', __('admin.advertisements_index'))
@section('breadcrumb') {{ __('admin.nav_advertisements') }} @endsection
@section('content')
<div style="display:flex;justify-content:space-between;margin-bottom:20px">
  <div></div>
  <a href="{{ route('admin.advertisements.create') }}" class="btn btn-primary"><i class="fa-solid fa-plus"></i> {{ __('admin.btn_new_ad') }}</a>
</div>
<div class="filter-bar">
  <form method="GET" style="display:flex;gap:10px;flex:1;flex-wrap:wrap">
    <select name="position" class="form-control" style="max-width:180px">
      <option value="">{{ __('admin.filter_all_positions') }}</option>
      @foreach(['header'=>__('admin.ad_pos_header'),'homepage'=>__('admin.ad_pos_homepage'),'sidebar'=>__('admin.ad_pos_sidebar'),'inside_article'=>__('admin.ad_pos_inside_article'),'footer'=>__('admin.ad_pos_footer'),'popup'=>__('admin.ad_pos_popup'),'video'=>__('admin.ad_pos_video')] as $v=>$l)
      <option value="{{ $v }}" {{ request('position')==$v?'selected':'' }}>{{ $l }}</option>
      @endforeach
    </select>
    <select name="status" class="form-control" style="max-width:150px">
      <option value="">{{ __('admin.filter_all_statuses') }}</option>
      <option value="1" {{ request('status')==='1'?'selected':'' }}>{{ __('admin.status_active') }}</option>
      <option value="0" {{ request('status')==='0'?'selected':'' }}>{{ __('admin.status_disabled') }}</option>
    </select>
    <button class="btn btn-secondary"><i class="fa-solid fa-search"></i></button>
  </form>
</div>
<div class="card">
  <div class="card-header"><span class="card-title">{{ __('admin.nav_advertisements') }} ({{ $ads->total() }})</span></div>
  <div class="table-wrap">
    <table>
      <thead><tr><th>{{ __('admin.col_title') }}</th><th>{{ __('admin.col_position') }}</th><th>{{ __('admin.col_type') }}</th><th>{{ __('admin.col_views') }}</th><th>{{ __('admin.col_clicks') }}</th><th>{{ __('admin.col_expires') }}</th><th>{{ __('admin.col_status') }}</th><th>{{ __('admin.col_actions') }}</th></tr></thead>
      <tbody>
        @forelse($ads as $ad)
        <tr>
          <td style="font-weight:700">{{ $ad->title }}</td>
          <td><span class="badge badge-info">{{ $ad->position }}</span></td>
          <td>{{ $ad->type }}</td>
          <td>{{ number_format($ad->views) }}</td>
          <td>{{ number_format($ad->clicks) }}</td>
          <td style="font-size:12px;color:#888">{{ $ad->ends_at ? $ad->ends_at->format('Y/m/d') : '∞' }}</td>
          <td>
            @if($ad->status && !$ad->is_expired)<span class="badge badge-success">{{ __('admin.status_active') }}</span>
            @elseif($ad->is_expired)<span class="badge badge-danger">{{ __('admin.status_expired') }}</span>
            @else<span class="badge badge-secondary">{{ __('admin.status_disabled') }}</span>@endif
          </td>
          <td>
            <div style="display:flex;gap:4px">
              <a href="{{ route('admin.advertisements.edit',$ad) }}" class="btn btn-primary btn-sm btn-icon"><i class="fa-solid fa-pen"></i></a>
              <form method="POST" action="{{ route('admin.advertisements.destroy',$ad) }}" onsubmit="return confirm('{{ __('admin.confirm_delete') }}')">
                @csrf @method('DELETE')
                <button class="btn btn-danger btn-sm btn-icon"><i class="fa-solid fa-trash"></i></button>
              </form>
            </div>
          </td>
        </tr>
        @empty
        <tr><td colspan="8"><div class="empty-state"><i class="fa-solid fa-rectangle-ad"></i><p>{{ __('admin.empty_ads') }}</p></div></td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  @if($ads->hasPages())<div style="padding:16px">{{ $ads->links() }}</div>@endif
</div>
@endsection
