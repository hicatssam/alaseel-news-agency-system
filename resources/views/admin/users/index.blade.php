@extends('layouts.admin')
@section('title', __('admin.users_index'))
@section('breadcrumb') {{ __('admin.nav_users') }} @endsection
@section('content')
<div style="display:flex;justify-content:space-between;margin-bottom:20px">
  <div></div>
  <a href="{{ route('admin.users.create') }}" class="btn btn-primary"><i class="fa-solid fa-plus"></i> {{ __('admin.users_create') }}</a>
</div>
<div class="filter-bar">
  <form method="GET" style="display:flex;gap:10px;flex:1">
    <input type="text" name="search" class="form-control" placeholder="{{ __('admin.placeholder_search_user') }}" value="{{ request('search') }}" style="max-width:320px">
    <button class="btn btn-secondary"><i class="fa-solid fa-search"></i></button>
  </form>
</div>
<div class="card">
  <div class="card-header"><span class="card-title">{{ __('admin.users_index') }} ({{ $users->total() }})</span></div>
  <div class="table-wrap">
    <table>
      <thead><tr><th>{{ __('admin.col_user') }}</th><th>{{ __('admin.col_email') }}</th><th>{{ __('admin.col_permissions') }}</th><th>{{ __('admin.col_status') }}</th><th>{{ __('admin.col_last_login') }}</th><th>{{ __('admin.col_actions') }}</th></tr></thead>
      <tbody>
        @forelse($users as $u)
        <tr>
          <td>
            <div style="display:flex;align-items:center;gap:10px">
              <div style="width:36px;height:36px;border-radius:50%;background:rgba(201,168,76,.15);display:flex;align-items:center;justify-content:center;font-weight:700;color:#c9a84c;flex-shrink:0">{{ mb_substr($u->name,0,1) }}</div>
              <div style="font-weight:700;color:#1a1a2e">{{ $u->name }}</div>
            </div>
          </td>
          <td style="font-size:13px;color:#666">{{ $u->email }}</td>
          <td>
            @foreach($u->roles as $role)
            <span class="badge badge-gold">{{ $role->name }}</span>
            @endforeach
          </td>
          <td>@if($u->status)<span class="badge badge-success">{{ __('admin.status_active') }}</span>@else<span class="badge badge-danger">{{ __('admin.status_disabled') }}</span>@endif</td>
          <td style="font-size:12px;color:#888">{{ $u->last_login_at?->format('Y/m/d H:i') ?? '—' }}</td>
          <td>
            <div style="display:flex;gap:4px">
              <a href="{{ route('admin.users.edit',$u) }}" class="btn btn-primary btn-sm btn-icon"><i class="fa-solid fa-pen"></i></a>
              @if($u->id !== auth()->id())
              <form method="POST" action="{{ route('admin.users.destroy',$u) }}" onsubmit="return confirm('{{ __('admin.confirm_delete_user') }}')">
                @csrf @method('DELETE')
                <button class="btn btn-danger btn-sm btn-icon"><i class="fa-solid fa-trash"></i></button>
              </form>
              @endif
            </div>
          </td>
        </tr>
        @empty
        <tr><td colspan="6"><div class="empty-state"><i class="fa-solid fa-users"></i><p>{{ __('admin.empty_users') }}</p></div></td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  @if($users->hasPages())<div style="padding:16px">{{ $users->links() }}</div>@endif
</div>
@endsection
