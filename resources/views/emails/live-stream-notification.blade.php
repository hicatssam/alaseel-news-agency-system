<!DOCTYPE html>
<html lang="{{ $emailLocale }}" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{ __('admin.email_live_stream_subject', ['title' => $stream->title]) }}</title>
  <style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body {
      font-family: {{ $isRtl ? "'Segoe UI', Tahoma, Arial, sans-serif" : "'Segoe UI', Arial, sans-serif" }};
      background: #f4f4f7;
      color: #333;
      direction: {{ $isRtl ? 'rtl' : 'ltr' }};
      text-align: {{ $isRtl ? 'right' : 'left' }};
    }
    .wrapper {
      max-width: 600px;
      margin: 40px auto;
      background: #ffffff;
      border-radius: 12px;
      overflow: hidden;
      box-shadow: 0 2px 16px rgba(0,0,0,0.08);
    }
    .header {
      background: linear-gradient(135deg, #1a1a2e 0%, #16213e 60%, #0f3460 100%);
      padding: 36px 40px 28px;
      text-align: center;
      position: relative;
    }
    .live-badge {
      display: inline-block;
      background: #e63946;
      color: #fff;
      font-size: 12px;
      font-weight: 700;
      letter-spacing: 2px;
      text-transform: uppercase;
      padding: 5px 14px;
      border-radius: 20px;
      margin-bottom: 14px;
      animation: none;
    }
    .live-dot {
      display: inline-block;
      width: 8px;
      height: 8px;
      background: #fff;
      border-radius: 50%;
      margin-{{ $isRtl ? 'left' : 'right' }}: 6px;
      vertical-align: middle;
    }
    .site-name {
      color: #c9a84c;
      font-size: 13px;
      font-weight: 600;
      letter-spacing: 1px;
      text-transform: uppercase;
      margin-bottom: 10px;
    }
    .stream-title {
      color: #ffffff;
      font-size: 22px;
      font-weight: 700;
      line-height: 1.4;
      margin-bottom: 0;
    }
    .body {
      padding: 36px 40px;
    }
    .heading {
      font-size: 18px;
      font-weight: 700;
      color: #1a1a2e;
      margin-bottom: 14px;
    }
    .description {
      font-size: 15px;
      color: #555;
      line-height: 1.7;
      margin-bottom: 20px;
    }
    .stream-description {
      background: #f8f9fa;
      border-{{ $isRtl ? 'right' : 'left' }}: 4px solid #c9a84c;
      padding: 14px 18px;
      border-radius: 4px;
      font-size: 14px;
      color: #444;
      line-height: 1.6;
      margin-bottom: 28px;
    }
    .cta-wrap {
      text-align: center;
      margin: 28px 0;
    }
    .cta-btn {
      display: inline-block;
      background: #e63946;
      color: #ffffff !important;
      text-decoration: none;
      font-size: 16px;
      font-weight: 700;
      padding: 14px 36px;
      border-radius: 8px;
      letter-spacing: 0.5px;
    }
    .divider {
      border: none;
      border-top: 1px solid #eee;
      margin: 28px 0 20px;
    }
    .footer {
      background: #f8f9fa;
      padding: 22px 40px;
      text-align: center;
      font-size: 12px;
      color: #999;
      line-height: 1.6;
    }
    .footer a {
      color: #c9a84c;
      text-decoration: underline;
    }
  </style>
</head>
<body>
  <div class="wrapper">
    <!-- Header -->
    <div class="header">
      <div class="site-name">{{ __('messages.site_name') }}</div>
      <div class="live-badge">
        <span class="live-dot"></span>
        {{ __('messages.live_badge') }}
      </div>
      <div class="stream-title">{{ $stream->title }}</div>
    </div>

    <!-- Body -->
    <div class="body">
      <div class="heading">{{ __('admin.email_live_stream_heading') }}</div>
      <p class="description">{{ __('admin.email_live_stream_body', ['site' => __('messages.site_name')]) }}</p>

      @if($stream->description)
      <div class="stream-description">{{ $stream->description }}</div>
      @endif

      <div class="cta-wrap">
        <a href="{{ url('/live') }}" class="cta-btn">
          {{ __('admin.email_live_stream_cta') }}
        </a>
      </div>

      <hr class="divider">

      <p style="font-size:13px;color:#888;text-align:center">
        {{ __('admin.email_live_stream_unsub') }}
        <a href="{{ $unsubscribeUrl }}" style="color:#c9a84c">
          {{ __('admin.email_live_stream_unsub_link') }}
        </a>
      </p>
    </div>

    <!-- Footer -->
    <div class="footer">
      &copy; {{ date('Y') }} {{ __('messages.site_name') }} &mdash; {{ __('messages.footer_copyright') }}<br>
      {{ __('messages.address_value') }}
    </div>
  </div>
</body>
</html>
