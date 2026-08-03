@extends('layouts.app')
@section('title',__('messages.contact_page_title'))
@section('content')
@php $isRtl = app()->getLocale() === 'ar'; @endphp
<div class="main-content">
<div class="container" style="max-width:900px">
  <h1 style="font-size:26px;font-weight:900;text-align:center;margin-bottom:8px;color:var(--white)">{{ __('messages.contact_page_title') }}</h1>
  <p style="text-align:center;color:rgba(255,255,255,.4);margin-bottom:32px">{{ __('messages.contact_page_sub') }}</p>

  <div style="display:grid;grid-template-columns:1fr 1.5fr;gap:28px">
    <div>
      <div class="sidebar-widget">
        <div style="padding:24px">
          <h3 style="font-size:16px;font-weight:700;margin-bottom:20px;color:var(--white)">{{ __('messages.contact_info') }}</h3>
          <div style="display:flex;flex-direction:column;gap:16px;font-size:14px">
            <div style="display:flex;gap:12px;align-items:flex-start">
              <div style="width:40px;height:40px;background:rgba(200,154,43,.12);border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0"><i class="fa-solid fa-location-dot" style="color:var(--gold)"></i></div>
              <div><div style="font-weight:700;margin-bottom:2px;color:var(--white)">{{ __('messages.address_label') }}</div><div style="color:rgba(255,255,255,.4)">{{ __('messages.address_value') }}</div></div>
            </div>
            <div style="display:flex;gap:12px;align-items:flex-start">
              <div style="width:40px;height:40px;background:rgba(200,154,43,.12);border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0"><i class="fa-solid fa-envelope" style="color:var(--gold)"></i></div>
              <div><div style="font-weight:700;margin-bottom:2px;color:var(--white)">{{ __('messages.email_label') }}</div><div style="color:rgba(255,255,255,.4)">info@alaseel.news</div></div>
            </div>
            <div style="display:flex;gap:12px;align-items:flex-start">
              <div style="width:40px;height:40px;background:rgba(200,154,43,.12);border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0"><i class="fa-solid fa-phone" style="color:var(--gold)"></i></div>
              <div><div style="font-weight:700;margin-bottom:2px;color:var(--white)">{{ __('messages.phone_label') }}</div><div style="color:rgba(255,255,255,.4)">{{ __('messages.phone_value') }}</div></div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div style="background:var(--surface);border:1px solid var(--border);border-radius:12px;padding:28px">
      @if(session('success'))
      <div class="alert alert-success"><i class="fa-solid fa-check-circle"></i> {{ session('success') }}</div>
      @endif
      @if($errors->any())
      <div class="alert alert-error"><i class="fa-solid fa-exclamation-circle"></i>{{ $errors->first() }}</div>
      @endif

      <form action="{{ route('contact.store') }}" method="POST">
        @csrf
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px">
          <div>
            <label style="font-size:13px;font-weight:700;color:rgba(255,255,255,.6);display:block;margin-bottom:6px">{{ __('messages.field_name') }}</label>
            <input type="text" name="name" style="width:100%;padding:11px;background:var(--surface2);border:1px solid var(--border);border-radius:8px;font-family:'Cairo',sans-serif;color:var(--white)" value="{{ old('name') }}" required>
          </div>
          <div>
            <label style="font-size:13px;font-weight:700;color:rgba(255,255,255,.6);display:block;margin-bottom:6px">{{ __('messages.field_email') }}</label>
            <input type="email" name="email" style="width:100%;padding:11px;background:var(--surface2);border:1px solid var(--border);border-radius:8px;font-family:'Cairo',sans-serif;color:var(--white)" value="{{ old('email') }}" required>
          </div>
        </div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px">
          <div>
            <label style="font-size:13px;font-weight:700;color:rgba(255,255,255,.6);display:block;margin-bottom:6px">{{ __('messages.field_phone') }}</label>
            <input type="text" name="phone" style="width:100%;padding:11px;background:var(--surface2);border:1px solid var(--border);border-radius:8px;font-family:'Cairo',sans-serif;color:var(--white)" value="{{ old('phone') }}">
          </div>
          <div>
            <label style="font-size:13px;font-weight:700;color:rgba(255,255,255,.6);display:block;margin-bottom:6px">{{ __('messages.field_subject') }}</label>
            <input type="text" name="subject" style="width:100%;padding:11px;background:var(--surface2);border:1px solid var(--border);border-radius:8px;font-family:'Cairo',sans-serif;color:var(--white)" value="{{ old('subject') }}">
          </div>
        </div>
        <div style="margin-bottom:20px">
          <label style="font-size:13px;font-weight:700;color:rgba(255,255,255,.6);display:block;margin-bottom:6px">{{ __('messages.field_message') }}</label>
          <textarea name="message" rows="6" style="width:100%;padding:11px;background:var(--surface2);border:1px solid var(--border);border-radius:8px;font-family:'Cairo',sans-serif;resize:vertical;direction:{{ $isRtl ? 'rtl' : 'ltr' }};color:var(--white)" required>{{ old('message') }}</textarea>
        </div>
        <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;padding:13px">
          <i class="fa-solid fa-paper-plane"></i> {{ __('messages.send_message') }}
        </button>
      </form>
    </div>
  </div>
</div>
</div>
@endsection
