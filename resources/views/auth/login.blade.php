@php $locale = app()->getLocale(); $isRtl = $locale === 'ar'; $dir = $isRtl ? 'rtl' : 'ltr'; @endphp
<!DOCTYPE html>
<html lang="{{ $locale }}" dir="{{ $dir }}">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{{ __('auth.sign_in') }} — ALASEEL</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700;900&family=Inter:wght@400;500;600;700&display=swap">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
*{box-sizing:border-box;margin:0;padding:0}
:root{--gold:#C89A2B;--gold-light:#F0C75E;--black:#0B0B0B;--surface:#151515;--surface2:#1B1B1B;--border:#2A2A2A;--text-muted:rgba(255,255,255,.45);--white:#fff}
html,body{height:100%}
body{font-family:'Inter',sans-serif;color:var(--white);min-height:100vh;display:flex;overflow-x:hidden;direction:ltr;
  background:url('https://images.unsplash.com/photo-1451187580459-43490279c0fa?w=1920&q=80') center/cover no-repeat fixed}
.bg-overlay{position:fixed;inset:0;z-index:0;
  background:linear-gradient(135deg,rgba(5,5,15,.92) 0%,rgba(10,8,5,.88) 50%,rgba(5,5,15,.95) 100%)}
.blob{position:absolute;border-radius:50%;filter:blur(120px);pointer-events:none;z-index:1}
.blob-amber{width:600px;height:600px;background:radial-gradient(circle,rgba(200,154,43,.22) 0%,transparent 70%);top:-120px;right:-100px}
.blob-gold-sm{width:280px;height:280px;background:radial-gradient(circle,rgba(200,154,43,.14) 0%,transparent 70%);bottom:10%;left:25%}
.lang-toggle{position:fixed;top:20px;left:20px;z-index:100;display:flex;align-items:center;background:rgba(255,255,255,.07);border:1px solid var(--border);border-radius:30px;padding:3px;gap:2px}
.lang-btn{padding:5px 14px;border-radius:24px;font-size:12px;font-weight:600;cursor:pointer;transition:.2s;color:rgba(255,255,255,.5);letter-spacing:.5px;font-family:'Inter',sans-serif;text-decoration:none;display:inline-block}
.lang-btn.active{background:var(--gold);color:var(--black)}
.login-layout{position:relative;z-index:1;display:flex;width:100%;min-height:100vh}
.left-panel{flex:1;display:flex;flex-direction:column;justify-content:center;padding:60px 60px 60px 80px;position:relative;overflow:hidden}
.brand-wrap{margin-bottom:60px}
.brand-logo{display:inline-flex;align-items:center;gap:12px;margin-bottom:8px}
.logo-badge{width:44px;height:44px;background:var(--gold);border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:18px;font-weight:900;color:var(--black);font-family:'Inter',sans-serif;letter-spacing:-1px}
.brand-name{font-family:'Inter',sans-serif;font-size:18px;font-weight:700;letter-spacing:2px;color:var(--white);text-transform:uppercase}
.brand-sub{font-size:11px;color:var(--gold);letter-spacing:2px;font-family:'Inter',sans-serif;text-transform:uppercase;opacity:.8}
.panel-heading{font-family:'Inter',sans-serif;font-size:42px;font-weight:700;line-height:1.2;margin-bottom:14px;color:var(--white)}
.panel-heading span{color:var(--gold)}
.panel-desc{font-size:15px;color:rgba(255,255,255,.5);line-height:1.7;max-width:380px;margin-bottom:56px}
.stats-row{display:flex;gap:40px}
.stat-item{border-right:2px solid var(--border);padding-right:32px}
.stat-item:first-child{border-right:none;padding-right:0}
.stat-num{font-family:'Inter',sans-serif;font-size:28px;font-weight:700;color:var(--gold-light);line-height:1}
.stat-num.green{color:#4ade80}
.stat-label{font-size:12px;color:rgba(255,255,255,.4);margin-top:5px;letter-spacing:.3px}
.v-divider{width:1px;background:var(--border);margin:40px 0;flex-shrink:0;align-self:stretch}
.right-panel{width:500px;flex-shrink:0;display:flex;align-items:center;justify-content:center;padding:40px 48px;position:relative}
.login-card{width:100%;background:rgba(255,255,255,.04);border:1px solid var(--border);border-radius:20px;padding:40px 36px;backdrop-filter:blur(20px);box-shadow:0 32px 80px rgba(0,0,0,.5)}
.card-title{font-family:'Inter',sans-serif;font-size:26px;font-weight:700;color:var(--white);margin-bottom:6px}
.card-sub{font-size:13px;color:var(--text-muted);margin-bottom:32px}
.form-group{margin-bottom:20px}
.form-label{display:block;font-size:12px;font-weight:600;color:rgba(255,255,255,.6);margin-bottom:8px;letter-spacing:.3px}
.input-wrap{position:relative}
.input-icon{position:absolute;top:50%;transform:translateY(-50%);right:14px;color:rgba(255,255,255,.3);font-size:14px;pointer-events:none}
.form-input{width:100%;padding:13px 42px 13px 14px;background:rgba(255,255,255,.06);border:1px solid var(--border);border-radius:10px;font-size:14px;font-family:'Cairo',sans-serif;color:var(--white);transition:.2s;direction:rtl}
.form-input::placeholder{color:rgba(255,255,255,.25)}
.form-input:focus{outline:none;border-color:var(--gold);background:rgba(200,154,43,.06);box-shadow:0 0 0 3px rgba(200,154,43,.12)}
.form-input:-webkit-autofill{-webkit-box-shadow:0 0 0 100px #1B1B1B inset;-webkit-text-fill-color:#fff}
.remember-row{display:flex;align-items:center;justify-content:space-between;margin-bottom:24px}
.check-label{display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px;color:rgba(255,255,255,.5)}
.check-label input{accent-color:var(--gold);width:14px;height:14px}
.btn-signin{width:100%;padding:14px;background:var(--gold);color:var(--black);border:none;border-radius:10px;font-size:15px;font-weight:700;font-family:'Cairo',sans-serif;cursor:pointer;transition:.2s;display:flex;align-items:center;justify-content:center;gap:8px;letter-spacing:.3px}
.btn-signin:hover{background:var(--gold-light);box-shadow:0 6px 24px rgba(200,154,43,.35)}
.btn-signin:active{transform:scale(.99)}
.alert-error{background:rgba(214,40,40,.12);border:1px solid rgba(214,40,40,.3);border-radius:10px;padding:12px 14px;margin-bottom:20px;font-size:13px;color:#ff7b7b;display:flex;align-items:center;gap:8px}
.back-link{text-align:center;margin-top:20px;font-size:13px;color:rgba(255,255,255,.35);display:flex;align-items:center;justify-content:center;gap:6px}
.back-link a{color:rgba(255,255,255,.5);transition:.2s;text-decoration:none}
.back-link a:hover{color:var(--gold)}
.page-footer{position:relative;padding:16px 24px;text-align:center;font-size:11px;color:rgba(255,255,255,.2);font-family:'Inter',sans-serif;z-index:1;letter-spacing:.3px}
.mobile-logo{display:none;text-align:center;padding:32px 0 16px;position:relative;z-index:1}
@media(max-width:900px){
  .left-panel{display:none}
  .right-panel{width:100%;padding:60px 28px 32px;display:flex;flex-direction:column;align-items:center;justify-content:flex-start}
  .login-card{width:100%;max-width:460px}
  .v-divider{display:none}
  .lang-toggle{left:16px;top:16px}
  .mobile-logo{display:block}
  .login-layout{min-height:100vh}
  body{display:flex;flex-direction:column}
}
@media(max-width:480px){
  .right-panel{padding:20px 16px 20px}
  .login-card{padding:28px 20px;border-radius:16px}
  .card-title{font-size:22px}
  .lang-toggle{top:14px;left:12px}
  .mobile-logo img{height:80px}
}
</style>
</head>
<body>
<div class="bg-overlay"></div>
<div class="blob blob-amber"></div>
<div class="blob blob-gold-sm"></div>


<!-- Language toggle -->
<div class="lang-toggle">
  <a href="{{ route('language.switch','en') }}" class="lang-btn {{ $locale==='en'?'active':'' }}">EN</a>
  <a href="{{ route('language.switch','ar') }}" class="lang-btn {{ $locale==='ar'?'active':'' }}">ع</a>
  <a href="{{ route('language.switch','fr') }}" class="lang-btn {{ $locale==='fr'?'active':'' }}">FR</a>
</div>

<div class="login-layout">
  <!-- Left panel -->
  <div class="left-panel">
    <div class="brand-wrap">
      <div class="brand-logo">
        <img src="{{ asset('images/logo.png') }}" alt="Al-Aseel" style="height:110px;width:auto;display:block;object-fit:contain">
      </div>
    </div>
    <h1 class="panel-heading">{{ __('auth.admin_panel') }}</h1>
    <p class="panel-desc">{{ __('auth.welcome_desc') }}</p>
  </div>

  <div class="v-divider"></div>

  <!-- Right panel -->
  <div class="right-panel">
    <div class="mobile-logo">
      <img src="{{ asset('images/logo.png') }}" alt="Al-Aseel" style="height:100px;width:auto;object-fit:contain">
    </div>
    <div class="login-card">
      <div class="card-title">{{ __('auth.sign_in') }}</div>
      <div class="card-sub">{{ __('auth.admin_panel') }}</div>

      @if($errors->any())
      <div class="alert-error">
        <i class="fa-solid fa-circle-exclamation"></i>
        {{ $errors->first() }}
      </div>
      @endif

      <form method="POST" action="{{ route('login') }}">
        @csrf
        <div class="form-group">
          <label class="form-label">{{ __('auth.email_label') }}</label>
          <div class="input-wrap">
            <i class="fa-regular fa-envelope input-icon"></i>
            <input type="email" name="email" class="form-input"
              value="{{ old('email','admin@alaseel.net') }}"
              placeholder="admin@alaseel.news" required autofocus autocomplete="username">
          </div>
        </div>
        <div class="form-group">
          <label class="form-label">{{ __('auth.password_label') }}</label>
          <div class="input-wrap">
            <i class="fa-solid fa-lock input-icon"></i>
            <input type="password" name="password" class="form-input"
              placeholder="••••••••" required autocomplete="current-password">
          </div>
        </div>
        <div class="remember-row">
          <label class="check-label">
            <input type="checkbox" name="remember"> {{ __('auth.remember_me') }}
          </label>
        </div>
        <button type="submit" class="btn-signin">
          {{ __('auth.sign_in') }}
        </button>
      </form>

      <div class="back-link">
        <i class="fa-solid fa-arrow-right-to-bracket" style="font-size:11px"></i>
        <a href="{{ route('home') }}">{{ __('auth.back_to_site') }}</a>
      </div>
    </div>
  </div>
</div>

<div class="page-footer">© {{ date('Y') }} ALASEEL - Voice of Truth</div>
</body>
</html>
