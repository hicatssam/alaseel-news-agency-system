@extends('layouts.admin')
@section('title', __('admin.nav_live_stream'))
@section('breadcrumb'){{ __('admin.nav_live_stream') }}@endsection
@section('content')
<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px">
  <div>
    <h1 style="font-size:20px;font-weight:700;color:var(--dark)">{{ __('admin.nav_live_stream') }}</h1>
    <p style="font-size:13px;color:#888;margin-top:3px">{{ __('admin.live_stream_subtitle') }}</p>
  </div>
  <a href="{{ route('admin.live-streams.create') }}" class="btn btn-primary">
    <i class="fa-solid fa-plus"></i> {{ __('admin.live_stream_new') }}
  </a>
</div>

<div class="card">
  <div class="table-wrap">
    <table>
      <thead>
        <tr>
          <th>{{ __('admin.col_title') }}</th>
          <th>{{ __('admin.col_embed_url') }}</th>
          <th>{{ __('admin.col_status') }}</th>
          <th>{{ __('admin.col_created') }}</th>
          <th>{{ __('admin.col_actions') }}</th>
        </tr>
      </thead>
      <tbody>
        @forelse($streams as $stream)
        <tr>
          <td>
            <div style="font-weight:600;color:var(--dark)">{{ $stream->title }}</div>
            @if($stream->description)
            <div style="font-size:12px;color:#888;margin-top:2px">{{ Str::limit($stream->description, 60) }}</div>
            @endif
          </td>
          <td>
            <a href="{{ $stream->embed_url }}" target="_blank" style="font-size:12px;color:var(--gold);word-break:break-all">
              {{ Str::limit($stream->embed_url, 50) }}
            </a>
          </td>
          <td>
            <form action="{{ route('admin.live-streams.toggle', $stream) }}" method="POST" style="display:inline">
              @csrf
              @method('PATCH')
              <button type="submit" class="badge {{ $stream->is_active ? 'badge-success' : 'badge-secondary' }}"
                      style="border:none;cursor:pointer;padding:5px 12px">
                @if($stream->is_active)
                  <span style="display:inline-block;width:8px;height:8px;border-radius:50%;background:#27ae60;animation:livePulse 1.4s infinite;margin-inline-end:5px"></span>
                  {{ __('admin.status_live') }}
                @else
                  {{ __('admin.status_offline') }}
                @endif
              </button>
            </form>
          </td>
          <td style="font-size:12px;color:#888">{{ $stream->created_at->format('Y/m/d') }}</td>
          <td>
            <div style="display:flex;gap:6px">
              <a href="{{ route('admin.live-streams.edit', $stream) }}" class="btn btn-outline btn-sm btn-icon" title="{{ __('admin.btn_edit') }}">
                <i class="fa-solid fa-pen"></i>
              </a>
              <form action="{{ route('admin.live-streams.destroy', $stream) }}" method="POST"
                    onsubmit="return confirm('{{ __('admin.confirm_delete') }}')">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-danger btn-sm btn-icon" title="{{ __('admin.btn_delete') }}">
                  <i class="fa-solid fa-trash"></i>
                </button>
              </form>
            </div>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="5">
            <div class="empty-state">
              <i class="fa-solid fa-tower-broadcast"></i>
              <p>{{ __('admin.live_stream_empty') }}</p>
            </div>
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

<style>
@keyframes livePulse{0%,100%{opacity:1}50%{opacity:.4}}
</style>
@endsection
