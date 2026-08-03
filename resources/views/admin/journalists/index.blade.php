@extends('layouts.admin')
@section('title', __('admin.journalists_index'))
@section('breadcrumb') {{ __('admin.nav_journalists') }} @endsection
@section('content')
<div style="display:flex;justify-content:space-between;margin-bottom:20px">
  <div></div>
  <a href="{{ route('admin.journalists.create') }}" class="btn btn-primary"><i class="fa-solid fa-plus"></i> {{ __('admin.journalists_create') }}</a>
</div>
<div class="filter-bar">
  <form method="GET" style="display:flex;gap:10px;flex:1">
    <input type="text" name="search" class="form-control" placeholder="{{ __('admin.placeholder_search_name') }}" value="{{ request('search') }}" style="max-width:280px">
    <select name="status" class="form-control" style="max-width:150px">
      <option value="">{{ __('admin.filter_all') }}</option>
      <option value="1" {{ request('status')==='1'?'selected':'' }}>{{ __('admin.status_active') }}</option>
      <option value="0" {{ request('status')==='0'?'selected':'' }}>{{ __('admin.status_disabled') }}</option>
    </select>
    <button class="btn btn-secondary"><i class="fa-solid fa-search"></i></button>
  </form>
</div>
<div class="card">
  <div class="card-header"><span class="card-title">{{ __('admin.journalists_index') }} ({{ $journalists->total() }})</span></div>
  <div class="table-wrap">
    <table>
      <thead><tr><th>{{ __('admin.col_journalist') }}</th><th>{{ __('admin.col_position') }}</th><th>{{ __('admin.col_email') }}</th><th>{{ __('admin.col_articles_count') }}</th><th>{{ __('admin.col_status') }}</th><th>{{ __('admin.col_actions') }}</th></tr></thead>
      <tbody>
        @forelse($journalists as $j)
        <tr>
          <td>
            <div style="display:flex;align-items:center;gap:10px">
              <div style="width:38px;height:38px;border-radius:50%;background:rgba(201,168,76,.15);display:flex;align-items:center;justify-content:center;font-weight:700;color:#c9a84c;font-size:14px;flex-shrink:0">{{ mb_substr($j->name,0,1) }}</div>
              <div><div style="font-weight:700;color:#1a1a2e">{{ $j->name }}</div></div>
            </div>
          </td>
          <td style="font-size:13px;color:#666">{{ $j->job_title ?? '—' }}</td>
          <td style="font-size:12px;color:#888">{{ $j->email ?? '—' }}</td>
          <td><span class="badge badge-gold">{{ $j->articles_count }}</span></td>
          <td>@if($j->status)<span class="badge badge-success">{{ __('admin.status_active') }}</span>@else<span class="badge badge-danger">{{ __('admin.status_disabled') }}</span>@endif</td>
          <td>
            <div style="display:flex;gap:4px">
              <a href="{{ route('admin.journalists.show',$j) }}" class="btn btn-outline btn-sm btn-icon"><i class="fa-solid fa-eye"></i></a>
              <a href="{{ route('admin.journalists.edit',$j) }}" class="btn btn-primary btn-sm btn-icon"><i class="fa-solid fa-pen"></i></a>
              <form method="POST" action="{{ route('admin.journalists.destroy',$j) }}" onsubmit="return confirm('{{ __('admin.confirm_delete_journalist') }}')">
                @csrf @method('DELETE')
                <button class="btn btn-danger btn-sm btn-icon"><i class="fa-solid fa-trash"></i></button>
              </form>
            </div>
          </td>
        </tr>
        @empty
        <tr><td colspan="6"><div class="empty-state"><i class="fa-solid fa-user-tie"></i><p>{{ __('admin.empty_journalists') }}</p></div></td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  @if($journalists->hasPages())<div style="padding:16px">{{ $journalists->links() }}</div>@endif
</div>
@endsection
