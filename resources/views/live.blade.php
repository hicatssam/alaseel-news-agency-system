@extends('layouts.app')
@section('title', $stream ? $stream->title : __('messages.live_page_title'))
@section('content')
@php $isRtl = app()->getLocale() === 'ar'; @endphp

<div class="main-content">
<div class="container">

  @if($stream)
  {{-- Active stream --}}
  <div style="margin-bottom:24px">
    <div style="display:flex;align-items:center;gap:12px;margin-bottom:10px">
      <span style="display:inline-flex;align-items:center;gap:6px;background:var(--red);color:#fff;font-size:12px;font-weight:700;padding:5px 12px;border-radius:20px;letter-spacing:.5px">
        <span style="width:8px;height:8px;border-radius:50%;background:#fff;display:inline-block;animation:livePulse 1.2s ease infinite"></span>
        {{ __('messages.live_badge') }}
      </span>
      @if($stream->viewers_label)
      <span style="font-size:13px;color:rgba(255,255,255,.5)">
        <i class="fa-solid fa-users" style="color:var(--gold)"></i> {{ $stream->viewers_label }}
      </span>
      @endif
    </div>
    <h1 style="font-size:22px;font-weight:900;color:var(--white);margin-bottom:6px">{{ $stream->title }}</h1>
    @if($stream->description)
    <p style="font-size:14px;color:rgba(255,255,255,.5);line-height:1.7">{{ $stream->description }}</p>
    @endif
  </div>

  {{-- Embed player --}}
  <div style="position:relative;padding-bottom:56.25%;height:0;overflow:hidden;border-radius:14px;border:1px solid var(--border);background:#000;margin-bottom:28px;box-shadow:0 16px 48px rgba(0,0,0,.6)">
    <iframe src="{{ $stream->embed_url }}"
            style="position:absolute;top:0;left:0;width:100%;height:100%;border:none"
            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
            allowfullscreen>
    </iframe>
  </div>

  {{-- Share --}}
  <div style="background:var(--surface);border:1px solid var(--border);border-radius:10px;padding:16px;display:flex;align-items:center;gap:12px;flex-wrap:wrap;margin-bottom:28px">
    <span style="font-size:13px;font-weight:700;color:rgba(255,255,255,.6)">{{ __('messages.share_stream') }}</span>
    <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->url()) }}" target="_blank"
       style="background:#1877f2;color:#fff;padding:7px 14px;border-radius:6px;font-size:12px;display:flex;align-items:center;gap:5px;font-weight:600">
      <i class="fa-brands fa-facebook-f"></i> Facebook
    </a>
    <a href="https://twitter.com/intent/tweet?text={{ urlencode($stream->title) }}&url={{ urlencode(request()->url()) }}" target="_blank"
       style="background:#000;color:#fff;padding:7px 14px;border-radius:6px;font-size:12px;display:flex;align-items:center;gap:5px;font-weight:600">
      <i class="fa-brands fa-x-twitter"></i> X
    </a>
    <a href="https://wa.me/?text={{ urlencode($stream->title.' '.request()->url()) }}" target="_blank"
       style="background:#25d366;color:#fff;padding:7px 14px;border-radius:6px;font-size:12px;display:flex;align-items:center;gap:5px;font-weight:600">
      <i class="fa-brands fa-whatsapp"></i> WhatsApp
    </a>
  </div>

  @else
  {{-- No active stream placeholder --}}
  <div style="text-align:center;padding:80px 20px;background:var(--surface);border:1px solid var(--border);border-radius:16px;margin-bottom:28px">
    <div style="width:80px;height:80px;border-radius:50%;background:rgba(200,154,43,.1);border:2px solid rgba(200,154,43,.2);display:flex;align-items:center;justify-content:center;margin:0 auto 20px">
      <i class="fa-solid fa-tower-broadcast" style="font-size:32px;color:rgba(200,154,43,.5)"></i>
    </div>
    <h2 style="font-size:20px;font-weight:900;color:var(--white);margin-bottom:10px">{{ __('messages.no_live_stream') }}</h2>
    <p style="font-size:14px;color:rgba(255,255,255,.4);max-width:400px;margin:0 auto 24px;line-height:1.7">{{ __('messages.no_live_stream_desc') }}</p>
    <a href="{{ route('home') }}" class="btn btn-primary"><i class="fa-solid fa-arrow-right"></i> {{ __('messages.nav_home') }}</a>
  </div>
  @endif

  {{-- Email notification subscription card --}}
  <div style="background:var(--surface);border:1px solid var(--border);border-radius:14px;padding:32px 36px">

    {{-- Flash messages --}}
    @if(session('subscribe_success'))
    <div style="background:rgba(34,197,94,.12);border:1px solid rgba(34,197,94,.25);border-radius:8px;padding:12px 16px;margin-bottom:20px;display:flex;align-items:center;gap:10px">
      <i class="fa-solid fa-circle-check" style="color:#22c55e"></i>
      <span style="font-size:14px;color:#22c55e;font-weight:600">{{ session('subscribe_success') }}</span>
    </div>
    @endif
    @if(session('subscribe_info'))
    <div style="background:rgba(200,154,43,.1);border:1px solid rgba(200,154,43,.25);border-radius:8px;padding:12px 16px;margin-bottom:20px;display:flex;align-items:center;gap:10px">
      <i class="fa-solid fa-circle-info" style="color:var(--gold)"></i>
      <span style="font-size:14px;color:var(--gold);font-weight:600">{{ session('subscribe_info') }}</span>
    </div>
    @endif

    <div style="display:flex;align-items:flex-start;gap:16px;margin-bottom:20px">
      <div style="width:44px;height:44px;border-radius:10px;background:rgba(200,154,43,.12);border:1px solid rgba(200,154,43,.2);display:flex;align-items:center;justify-content:center;flex-shrink:0">
        <i class="fa-solid fa-bell" style="color:var(--gold);font-size:18px"></i>
      </div>
      <div>
        <h3 style="font-size:16px;font-weight:800;color:var(--white);margin:0 0 4px">{{ __('messages.live_notify_title') }}</h3>
        <p style="font-size:13px;color:rgba(255,255,255,.45);margin:0;line-height:1.6">{{ __('messages.live_notify_desc') }}</p>
      </div>
    </div>

    @if(!session('subscribe_success'))
    <form action="{{ route('newsletter.subscribe') }}" method="POST" style="display:flex;gap:10px;flex-wrap:wrap">
      @csrf
      <input type="email" name="email" required
             placeholder="{{ __('messages.live_notify_placeholder') }}"
             value="{{ old('email') }}"
             style="flex:1;min-width:200px;background:rgba(255,255,255,.06);border:1px solid var(--border);border-radius:8px;padding:11px 16px;font-size:14px;color:var(--white);outline:none">
      <button type="submit" class="btn btn-primary" style="white-space:nowrap">
        <i class="fa-solid fa-bell"></i> {{ __('messages.live_notify_btn') }}
      </button>
    </form>
    @if($errors->has('email'))
    <p style="font-size:12px;color:#f87171;margin:8px 0 0">{{ $errors->first('email') }}</p>
    @endif
    @endif

  </div>

</div>
</div>

<style>
@keyframes livePulse{0%,100%{opacity:1}50%{opacity:.3}}
</style>
@endsection
