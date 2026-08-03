@extends('layouts.admin')
@section('title', __('admin.activity_logs_index'))
@section('breadcrumb') {{ __('admin.nav_activity_logs') }} @endsection
@section('content')
<div class="filter-bar">
  <form method="GET" style="display:flex;gap:10px;flex:1;flex-wrap:wrap">
    <select name="module" class="form-control" style="max-width:180px">
      <option value="">{{ __('admin.filter_all_modules') }}</option>
      @foreach($modules as $m)
      <option value="{{ $m }}" {{ request('module')==$m?'selected':'' }}>{{ $m }}</option>
      @endforeach
    </select>
    <button class="btn btn-secondary"><i class="fa-solid fa-search"></i></button>
  </form>
</div>
<div class="card">
  <div class="card-header"><span class="card-title">{{ __('admin.nav_activity_logs') }} ({{ $logs->total() }})</span></div>
  <div class="table-wrap">
    <table>
      <thead><tr><th>{{ __('admin.col_user') }}</th><th>{{ __('admin.col_action') }}</th><th>{{ __('admin.col_module') }}</th><th>{{ __('admin.col_details') }}</th><th>{{ __('admin.col_ip') }}</th><th>{{ __('admin.col_date') }}</th></tr></thead>
      <tbody>
        @forelse($logs as $log)
        <tr>
          <td style="font-weight:600">{{ $log->user?->name ?? __('admin.label_system') }}</td>
          <td><span class="badge {{ $log->action=='create'?'badge-success':($log->action=='delete'?'badge-danger':'badge-info') }}">{{ $log->action }}</span></td>
          <td><span class="badge badge-secondary">{{ $log->module }}</span></td>
          <td style="font-size:13px;color:#555">{{ Str::limit($log->description,70) }}</td>
          <td style="font-size:12px;color:#888">{{ $log->ip_address ?? '—' }}</td>
          <td style="font-size:12px;color:#888;white-space:nowrap">{{ $log->created_at->format('Y/m/d H:i') }}</td>
        </tr>
        @empty
        <tr><td colspan="6"><div class="empty-state"><i class="fa-solid fa-clock-rotate-left"></i><p>{{ __('admin.empty_activity_logs') }}</p></div></td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  @if($logs->hasPages())<div style="padding:16px">{{ $logs->links() }}</div>@endif
</div>
@endsection
