<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{{ __('messages.live_email_subject', ['title' => $stream->title]) }}</title>
<style>
  body { margin:0; padding:0; background:#0d0d0d; font-family: {{ app()->getLocale() === 'ar' ? "'Cairo', 'Segoe UI', Arial" : "'Inter', 'Segoe UI', Arial" }}, sans-serif; color:#e5e5e5; }
  .wrapper { max-width:600px; margin:40px auto; background:#1a1a1a; border-radius:16px; overflow:hidden; border:1px solid #2a2a2a; }
  .header { background:linear-gradient(135deg,#1a1a1a 0%,#2a1a00 100%); padding:36px 40px 28px; border-bottom:1px solid #2a2a2a; text-align:{{ app()->getLocale() === 'ar' ? 'right' : 'left' }}; }
  .logo { font-size:22px; font-weight:900; color:#c89a2b; letter-spacing:1px; }
  .logo span { color:#fff; }
  .live-badge { display:inline-flex; align-items:center; gap:6px; background:#e53e3e; color:#fff; font-size:11px; font-weight:800; padding:4px 12px; border-radius:20px; letter-spacing:1px; margin-top:12px; }
  .live-dot { width:7px; height:7px; border-radius:50%; background:#fff; animation:pulse 1.2s ease infinite; }
  .body { padding:36px 40px; text-align:{{ app()->getLocale() === 'ar' ? 'right' : 'left' }}; }
  .greeting { font-size:26px; font-weight:900; color:#fff; margin:0 0 8px; line-height:1.3; }
  .desc { font-size:15px; color:rgba(255,255,255,.55); line-height:1.8; margin:0 0 28px; }
  .stream-card { background:#111; border:1px solid #2a2a2a; border-radius:12px; padding:20px 24px; margin-bottom:28px; }
  .stream-title { font-size:18px; font-weight:800; color:#fff; margin:0 0 6px; }
  .stream-desc { font-size:13px; color:rgba(255,255,255,.45); line-height:1.7; margin:0; }
  .cta { display:inline-block; background:#c89a2b; color:#000; font-size:15px; font-weight:800; padding:14px 32px; border-radius:10px; text-decoration:none; letter-spacing:.3px; }
  .footer { padding:24px 40px; border-top:1px solid #2a2a2a; text-align:{{ app()->getLocale() === 'ar' ? 'right' : 'left' }}; }
  .footer p { font-size:12px; color:rgba(255,255,255,.25); margin:0 0 6px; line-height:1.6; }
  .footer a { color:rgba(255,255,255,.35); text-decoration:underline; }
  @keyframes pulse { 0%,100%{opacity:1} 50%{opacity:.3} }
</style>
</head>
<body>
<div class="wrapper">

  <div class="header">
    <div class="logo">الأصيل <span style="font-weight:400;font-size:14px;color:rgba(255,255,255,.4)">| AL-ASEEL</span></div>
    <div class="live-badge">
      <span class="live-dot"></span>
      {{ strtoupper(__('messages.live_badge')) }}
    </div>
  </div>

  <div class="body">
    <h1 class="greeting">{{ __('messages.live_email_greeting') }}</h1>
    <p class="desc">{{ __('messages.live_email_body', ['title' => $stream->title]) }}</p>

    <div class="stream-card">
      <p class="stream-title">{{ $stream->title }}</p>
      @if($stream->description)
      <p class="stream-desc">{{ $stream->description }}</p>
      @endif
    </div>

    <a href="{{ url('/live') }}" class="cta">
      {{ __('messages.live_email_cta') }} &rarr;
    </a>
  </div>

  <div class="footer">
    <p>{{ __('messages.site_name') }}</p>
    <p>
      <a href="{{ url('/newsletter/unsubscribe?email=') }}{{ $recipientEmail ?? '' }}">
        {{ __('messages.live_email_unsub') }}
      </a>
    </p>
  </div>

</div>
</body>
</html>
