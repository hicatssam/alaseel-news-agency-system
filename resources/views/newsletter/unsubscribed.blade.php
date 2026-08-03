@extends('layouts.app')
@section('title', __('messages.newsletter_unsub_title'))

@section('content')
<div style="min-height:60vh;display:flex;align-items:center;justify-content:center;padding:40px 20px">
  <div style="max-width:480px;text-align:center">
    <div style="font-size:52px;margin-bottom:20px">📭</div>
    <h1 style="font-size:24px;font-weight:700;color:#1a1a2e;margin-bottom:12px">
      {{ __('messages.newsletter_unsub_heading') }}
    </h1>
    <p style="color:#666;font-size:15px;line-height:1.7;margin-bottom:28px">
      {{ __('messages.newsletter_unsub_body') }}
    </p>
    <a href="{{ route('home') }}"
       style="display:inline-block;background:#1a1a2e;color:#fff;text-decoration:none;padding:12px 28px;border-radius:8px;font-size:14px;font-weight:600">
      {{ __('messages.nav_home') }}
    </a>
  </div>
</div>
@endsection
