@extends('layouts.admin')

@section('title', __('admin.notif_title'))
@section('breadcrumb') {{ __('admin.notif_title') }} @endsection

@section('content')
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;flex-wrap:wrap;gap:12px">
  <h2 style="font-size:1.25rem;font-weight:700;margin:0">{{ __('admin.notif_title') }}</h2>
  <div style="display:flex;gap:10px;flex-wrap:wrap">
    @if($notifications->where('read_at',null)->count())
    <form method="POST" action="{{ route('admin.notifications.read-all') }}">
      @csrf
      <button type="submit" class="btn btn-sm" style="background:var(--gold,#c89a2b);color:#111;border:none;cursor:pointer">
        <i class="fa-solid fa-check-double"></i> {{ __('admin.notif_mark_all_read') }}
      </button>
    </form>
    @endif
    @if($notifications->where('read_at','!=',null)->count())
    <form method="POST" action="{{ route('admin.notifications.clear-read') }}">
      @csrf
      @method('DELETE')
      <button type="submit" class="btn btn-sm btn-danger"
        onclick="return confirm('{{ __('admin.notif_confirm_clear') }}')"
        style="background:#c0392b;color:#fff;border:none;cursor:pointer">
        <i class="fa-solid fa-trash"></i> {{ __('admin.notif_clear_read') }}
      </button>
    </form>
    @endif
  </div>
</div>

@php
$typeIcon  = ['article'=>'fa-newspaper','contact'=>'fa-envelope','user'=>'fa-user','comment'=>'fa-comment','newsletter'=>'fa-bell'];
$typeColor = ['article'=>'#c89a2b','contact'=>'#3498db','user'=>'#27ae60','comment'=>'#e67e22','newsletter'=>'#9b59b6'];
$typeUrl   = ['article'=>route('admin.articles.index'),'contact'=>route('admin.contact.index'),'user'=>route('admin.users.index'),'comment'=>route('admin.comments.index'),'newsletter'=>route('admin.newsletter.index')];
@endphp

@if($notifications->isEmpty())
<div style="text-align:center;padding:80px 20px;color:#666">
  <i class="fa-solid fa-bell-slash" style="font-size:3rem;margin-bottom:16px;display:block;opacity:.4"></i>
  {{ __('admin.notif_empty') }}
</div>
@else
<div class="card" style="padding:0;overflow:hidden">
  @foreach($notifications as $notif)
  @php
    $icon  = $typeIcon[$notif->type]  ?? 'fa-circle-info';
    $color = $typeColor[$notif->type] ?? '#888';
    $url   = $typeUrl[$notif->type]   ?? '#';
  @endphp
  <div style="display:flex;align-items:flex-start;gap:14px;padding:16px 20px;border-bottom:1px solid rgba(255,255,255,.06);background:{{ $notif->read_at ? 'transparent' : 'rgba(200,154,43,.06)' }};transition:.2s">
    <div style="width:38px;height:38px;border-radius:50%;background:{{ $color }}22;display:flex;align-items:center;justify-content:center;flex-shrink:0;margin-top:2px">
      <i class="fa-solid {{ $icon }}" style="color:{{ $color }};font-size:.9rem"></i>
    </div>
    <div style="flex:1;min-width:0">
      <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap">
        <span style="font-weight:{{ $notif->read_at ? '400' : '600' }};font-size:.95rem">{{ $notif->title }}</span>
        @if(!$notif->read_at)
        <span style="display:inline-block;width:8px;height:8px;border-radius:50%;background:#c89a2b;flex-shrink:0"></span>
        @endif
      </div>
      <div style="color:#aaa;font-size:.83rem;margin-top:3px">{{ $notif->message }}</div>
      <div style="color:#666;font-size:.78rem;margin-top:5px">{{ $notif->created_at->diffForHumans() }}</div>
    </div>
    <div style="display:flex;gap:8px;flex-shrink:0;margin-top:2px">
      @if(!$notif->read_at)
      <form method="POST" action="{{ route('admin.notifications.read', $notif) }}" style="margin:0">
        @csrf
        <button type="submit" title="{{ __('admin.notif_mark_read') }}"
          style="background:none;border:1px solid rgba(255,255,255,.15);color:#aaa;border-radius:6px;padding:4px 8px;cursor:pointer;font-size:.78rem">
          <i class="fa-solid fa-check"></i>
        </button>
      </form>
      @endif
      <a href="{{ $url }}" style="border:1px solid rgba(255,255,255,.15);color:#aaa;border-radius:6px;padding:4px 8px;font-size:.78rem;text-decoration:none" title="{{ __('admin.notif_view') }}">
        <i class="fa-solid fa-arrow-up-right-from-square"></i>
      </a>
      <form method="POST" action="{{ route('admin.notifications.destroy', $notif) }}" style="margin:0">
        @csrf
        @method('DELETE')
        <button type="submit" title="{{ __('admin.notif_delete') }}"
          style="background:none;border:1px solid rgba(255,255,255,.15);color:#e74c3c;border-radius:6px;padding:4px 8px;cursor:pointer;font-size:.78rem">
          <i class="fa-solid fa-xmark"></i>
        </button>
      </form>
    </div>
  </div>
  @endforeach
</div>
{{ $notifications->links() }}
@endif
@endsection
