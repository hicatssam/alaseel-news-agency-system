@php $locale = app()->getLocale(); $isRtl = $locale === 'ar'; $dir = $isRtl ? 'rtl' : 'ltr'; @endphp

@php
    $socialLinks = [
        'facebook_url' => [
            'icon' => 'fa-brands fa-facebook-f',
            'label' => 'Facebook',
        ],
        'twitter_url' => [
            'icon' => 'fa-brands fa-x-twitter',
            'label' => 'X',
        ],
        'youtube_url' => [
            'icon' => 'fa-brands fa-youtube',
            'label' => 'YouTube',
        ],
        'instagram_url' => [
            'icon' => 'fa-brands fa-instagram',
            'label' => 'Instagram',
        ],
        'linkedin_url' => [
            'icon' => 'fa-brands fa-linkedin-in',
            'label' => 'LinkedIn',
        ],
        'tiktok_url' => [
            'icon' => 'fa-brands fa-tiktok',
            'label' => 'TikTok',
        ],
        'telegram_url' => [
            'icon' => 'fa-brands fa-telegram',
            'label' => 'Telegram',
        ],
        'whatsapp_url' => [
            'icon' => 'fa-brands fa-whatsapp',
            'label' => 'WhatsApp',
        ],
    ];
@endphp
<!DOCTYPE html>
<html lang="{{ $locale }}" dir="{{ $dir }}">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>@yield('title',__('messages.site_name')) — {{ __('messages.site_tagline') }}</title>
<meta name="description" content="@yield('description',__('messages.site_name'))">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700;900&family=Inter:wght@400;500;600;700&display=swap">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
*{box-sizing:border-box;margin:0;padding:0}
:root{
  --gold:#C89A2B;--gold-light:#F0C75E;--black:#0B0B0B;--surface:#151515;
  --surface2:#1B1B1B;--border:#2A2A2A;--white:#fff;--red:#D62828;
  --text:#E8E8E8;--text-muted:#B9B9B9
}
html{background:var(--black)}
html[dir="rtl"] body{font-family:'Cairo',sans-serif}
html[dir="ltr"] body{font-family:'Inter',sans-serif}
body{background:transparent;color:var(--text);position:relative;overflow-x:hidden;min-height:100vh;isolation:isolate}
a{text-decoration:none;color:inherit}
img{max-width:100%}

/* ── Global animated world-map background ───────────────── */
body::before{
  content:'';
  position:fixed;
  inset:0;
  z-index:-1;
  pointer-events:none;
  background-color:var(--black);
  background-image:
    radial-gradient(circle at 18% 22%, rgba(200,154,43,.12), transparent 55%),
    radial-gradient(circle at 82% 78%, rgba(200,154,43,.08), transparent 55%),
    url("data:image/svg+xml,%3Csvg xmlns='http://www\\.w3.org/2000/svg' width='960' height='480' viewBox='0 0 960 480'%3E%3Cg fill='%23C89A2B' fill-opacity='.55'%3E%3Ccircle cx='138' cy='108' r='2.2'/%3E%3Ccircle cx='147' cy='93' r='2.1'/%3E%3Ccircle cx='159' cy='213' r='1.7'/%3E%3Ccircle cx='117' cy='182' r='2.4'/%3E%3Ccircle cx='189' cy='146' r='2.4'/%3E%3Ccircle cx='131' cy='107' r='1.7'/%3E%3Ccircle cx='110' cy='174' r='2.1'/%3E%3Ccircle cx='149' cy='170' r='1.6'/%3E%3Ccircle cx='84' cy='116' r='2.2'/%3E%3Ccircle cx='159' cy='134' r='2.1'/%3E%3Ccircle cx='165' cy='131' r='2.3'/%3E%3Ccircle cx='215' cy='122' r='2.1'/%3E%3Ccircle cx='179' cy='221' r='2.2'/%3E%3Ccircle cx='96' cy='149' r='2.2'/%3E%3Ccircle cx='104' cy='161' r='1.6'/%3E%3Ccircle cx='209' cy='204' r='2.1'/%3E%3Ccircle cx='251' cy='134' r='2.2'/%3E%3Ccircle cx='194' cy='174' r='1.9'/%3E%3Ccircle cx='168' cy='188' r='1.6'/%3E%3Ccircle cx='215' cy='185' r='2.4'/%3E%3Ccircle cx='240' cy='129' r='1.9'/%3E%3Ccircle cx='167' cy='110' r='1.7'/%3E%3Ccircle cx='99' cy='123' r='1.9'/%3E%3Ccircle cx='164' cy='170' r='2.3'/%3E%3Ccircle cx='239' cy='219' r='1.8'/%3E%3Ccircle cx='156' cy='140' r='2.3'/%3E%3Ccircle cx='108' cy='120' r='1.8'/%3E%3Ccircle cx='171' cy='176' r='1.8'/%3E%3Ccircle cx='147' cy='173' r='2.4'/%3E%3Ccircle cx='213' cy='165' r='2.1'/%3E%3Ccircle cx='210' cy='93' r='2.3'/%3E%3Ccircle cx='231' cy='221' r='2.3'/%3E%3Ccircle cx='152' cy='146' r='1.7'/%3E%3Ccircle cx='201' cy='93' r='1.6'/%3E%3Ccircle cx='240' cy='266' r='1.8'/%3E%3Ccircle cx='234' cy='257' r='1.9'/%3E%3Ccircle cx='273' cy='263' r='1.8'/%3E%3Ccircle cx='251' cy='297' r='1.7'/%3E%3Ccircle cx='261' cy='315' r='1.7'/%3E%3Ccircle cx='231' cy='294' r='1.8'/%3E%3Ccircle cx='291' cy='266' r='1.6'/%3E%3Ccircle cx='302' cy='323' r='1.7'/%3E%3Ccircle cx='267' cy='245' r='2.1'/%3E%3Ccircle cx='281' cy='281' r='1.9'/%3E%3Ccircle cx='236' cy='360' r='2.1'/%3E%3Ccircle cx='288' cy='291' r='1.8'/%3E%3Ccircle cx='294' cy='366' r='2.4'/%3E%3Ccircle cx='284' cy='276' r='2.1'/%3E%3Ccircle cx='225' cy='284' r='1.8'/%3E%3Ccircle cx='260' cy='386' r='2.4'/%3E%3Ccircle cx='302' cy='297' r='1.8'/%3E%3Ccircle cx='242' cy='270' r='1.7'/%3E%3Ccircle cx='275' cy='381' r='2.3'/%3E%3Ccircle cx='263' cy='342' r='2.3'/%3E%3Ccircle cx='285' cy='315' r='1.7'/%3E%3Ccircle cx='288' cy='293' r='2.3'/%3E%3Ccircle cx='303' cy='302' r='1.9'/%3E%3Ccircle cx='237' cy='260' r='1.7'/%3E%3Ccircle cx='252' cy='326' r='1.7'/%3E%3Ccircle cx='276' cy='323' r='2.4'/%3E%3Ccircle cx='501' cy='146' r='2.3'/%3E%3Ccircle cx='482' cy='93' r='1.8'/%3E%3Ccircle cx='483' cy='122' r='1.8'/%3E%3Ccircle cx='500' cy='83' r='2.3'/%3E%3Ccircle cx='494' cy='111' r='2.1'/%3E%3Ccircle cx='543' cy='108' r='2.3'/%3E%3Ccircle cx='507' cy='117' r='2.1'/%3E%3Ccircle cx='534' cy='87' r='1.9'/%3E%3Ccircle cx='528' cy='119' r='1.8'/%3E%3Ccircle cx='509' cy='119' r='2.2'/%3E%3Ccircle cx='471' cy='119' r='1.8'/%3E%3Ccircle cx='488' cy='137' r='2.1'/%3E%3Ccircle cx='513' cy='137' r='2.3'/%3E%3Ccircle cx='503' cy='123' r='2.1'/%3E%3Ccircle cx='509' cy='131' r='1.9'/%3E%3Ccircle cx='510' cy='113' r='2.4'/%3E%3Ccircle cx='525' cy='146' r='2.4'/%3E%3Ccircle cx='486' cy='119' r='2.4'/%3E%3Ccircle cx='552' cy='185' r='1.7'/%3E%3Ccircle cx='510' cy='171' r='1.8'/%3E%3Ccircle cx='534' cy='185' r='2.3'/%3E%3Ccircle cx='567' cy='201' r='2.4'/%3E%3Ccircle cx='506' cy='255' r='2.4'/%3E%3Ccircle cx='552' cy='189' r='1.9'/%3E%3Ccircle cx='518' cy='225' r='1.7'/%3E%3Ccircle cx='497' cy='303' r='1.6'/%3E%3Ccircle cx='522' cy='246' r='1.6'/%3E%3Ccircle cx='498' cy='284' r='2.1'/%3E%3Ccircle cx='474' cy='210' r='1.6'/%3E%3Ccircle cx='546' cy='212' r='1.7'/%3E%3Ccircle cx='507' cy='342' r='2.3'/%3E%3Ccircle cx='491' cy='186' r='2.3'/%3E%3Ccircle cx='524' cy='299' r='1.7'/%3E%3Ccircle cx='509' cy='171' r='2.4'/%3E%3Ccircle cx='531' cy='320' r='1.7'/%3E%3Ccircle cx='555' cy='249' r='1.8'/%3E%3Ccircle cx='522' cy='345' r='1.8'/%3E%3Ccircle cx='476' cy='264' r='1.8'/%3E%3Ccircle cx='474' cy='189' r='1.6'/%3E%3Ccircle cx='485' cy='219' r='1.8'/%3E%3Ccircle cx='545' cy='215' r='2.1'/%3E%3Ccircle cx='482' cy='227' r='1.6'/%3E%3Ccircle cx='542' cy='269' r='1.7'/%3E%3Ccircle cx='513' cy='347' r='1.7'/%3E%3Ccircle cx='551' cy='245' r='1.9'/%3E%3Ccircle cx='552' cy='236' r='2.1'/%3E%3Ccircle cx='500' cy='326' r='2.2'/%3E%3Ccircle cx='531' cy='239' r='1.8'/%3E%3Ccircle cx='468' cy='183' r='1.6'/%3E%3Ccircle cx='542' cy='209' r='1.7'/%3E%3Ccircle cx='557' cy='293' r='1.8'/%3E%3Ccircle cx='488' cy='216' r='1.9'/%3E%3Ccircle cx='596' cy='146' r='1.8'/%3E%3Ccircle cx='704' cy='107' r='2.4'/%3E%3Ccircle cx='638' cy='129' r='1.6'/%3E%3Ccircle cx='657' cy='152' r='2.1'/%3E%3Ccircle cx='608' cy='158' r='1.6'/%3E%3Ccircle cx='626' cy='77' r='1.9'/%3E%3Ccircle cx='636' cy='105' r='2.1'/%3E%3Ccircle cx='698' cy='204' r='2.2'/%3E%3Ccircle cx='660' cy='123' r='2.4'/%3E%3Ccircle cx='594' cy='200' r='2.2'/%3E%3Ccircle cx='798' cy='180' r='2.2'/%3E%3Ccircle cx='776' cy='87' r='2.1'/%3E%3Ccircle cx='692' cy='221' r='2.3'/%3E%3Ccircle cx='780' cy='173' r='2.3'/%3E%3Ccircle cx='741' cy='194' r='1.8'/%3E%3Ccircle cx='651' cy='80' r='2.3'/%3E%3Ccircle cx='707' cy='180' r='2.1'/%3E%3Ccircle cx='740' cy='155' r='1.6'/%3E%3Ccircle cx='773' cy='204' r='2.1'/%3E%3Ccircle cx='699' cy='186' r='1.6'/%3E%3Ccircle cx='756' cy='108' r='1.7'/%3E%3Ccircle cx='626' cy='200' r='1.7'/%3E%3Ccircle cx='689' cy='134' r='1.9'/%3E%3Ccircle cx='741' cy='207' r='2.1'/%3E%3Ccircle cx='729' cy='75' r='1.7'/%3E%3Ccircle cx='623' cy='203' r='1.8'/%3E%3Ccircle cx='708' cy='63' r='1.6'/%3E%3Ccircle cx='626' cy='189' r='2.2'/%3E%3Ccircle cx='738' cy='116' r='2.1'/%3E%3Ccircle cx='680' cy='150' r='1.7'/%3E%3Ccircle cx='798' cy='99' r='2.4'/%3E%3Ccircle cx='678' cy='218' r='2.4'/%3E%3Ccircle cx='677' cy='111' r='1.7'/%3E%3Ccircle cx='713' cy='87' r='2.1'/%3E%3Ccircle cx='779' cy='158' r='2.3'/%3E%3Ccircle cx='746' cy='105' r='2.3'/%3E%3Ccircle cx='686' cy='65' r='1.6'/%3E%3Ccircle cx='687' cy='147' r='1.8'/%3E%3Ccircle cx='591' cy='126' r='1.8'/%3E%3Ccircle cx='632' cy='132' r='1.9'/%3E%3Ccircle cx='651' cy='143' r='1.8'/%3E%3Ccircle cx='566' cy='80' r='2.3'/%3E%3Ccircle cx='621' cy='111' r='2.1'/%3E%3Ccircle cx='605' cy='132' r='2.4'/%3E%3Ccircle cx='750' cy='69' r='2.2'/%3E%3Ccircle cx='677' cy='204' r='2.2'/%3E%3Ccircle cx='632' cy='69' r='2.3'/%3E%3Ccircle cx='587' cy='150' r='1.8'/%3E%3Ccircle cx='635' cy='203' r='2.4'/%3E%3Ccircle cx='624' cy='186' r='1.8'/%3E%3Ccircle cx='707' cy='135' r='1.7'/%3E%3Ccircle cx='597' cy='101' r='2.3'/%3E%3Ccircle cx='689' cy='102' r='2.3'/%3E%3Ccircle cx='591' cy='98' r='1.7'/%3E%3Ccircle cx='647' cy='78' r='1.8'/%3E%3Ccircle cx='624' cy='170' r='2.3'/%3E%3Ccircle cx='827' cy='342' r='1.9'/%3E%3Ccircle cx='804' cy='339' r='1.8'/%3E%3Ccircle cx='756' cy='332' r='2.4'/%3E%3Ccircle cx='764' cy='348' r='2.1'/%3E%3Ccircle cx='839' cy='327' r='1.8'/%3E%3Ccircle cx='776' cy='341' r='1.9'/%3E%3Ccircle cx='842' cy='347' r='2.1'/%3E%3Ccircle cx='765' cy='350' r='2.2'/%3E%3Ccircle cx='816' cy='368' r='1.9'/%3E%3Ccircle cx='830' cy='329' r='2.3'/%3E%3Ccircle cx='816' cy='335' r='1.7'/%3E%3Ccircle cx='776' cy='359' r='2.2'/%3E%3Ccircle cx='804' cy='354' r='1.9'/%3E%3Ccircle cx='773' cy='356' r='1.6'/%3E%3Ccircle cx='782' cy='345' r='2.4'/%3E%3Ccircle cx='816' cy='375' r='1.9'/%3E%3C/g%3E%3C/svg%3E");
  background-repeat:no-repeat,no-repeat,repeat;
  background-position:0 0,0 0,center;
  background-size:auto,auto,960px 480px;
  animation:siteMapPan 140s linear infinite;
}
@keyframes siteMapPan{
  from{background-position:0 0,0 0,0 0}
  to{background-position:0 0,0 0,-960px -480px}
}
@media(prefers-reduced-motion:reduce){body::before{animation:none}}

/* Clear animated world map (overrides the old scattered-dot pattern) */
body::before{
  opacity:.42;
  background-color:var(--black);
  background-image:
    radial-gradient(circle at 50% 45%,rgba(200,154,43,.09),transparent 60%),
    url("data:image/svg+xml,%3Csvg xmlns='http://www\\.w3.org/2000/svg' viewBox='0 0 1200 600'%3E%3Cg fill='%23C89A2B' fill-opacity='.16' stroke='%23C89A2B' stroke-opacity='.62' stroke-width='2'%3E%3Cpath d='M82 145l39-50 69-27 78 8 42 30 45 7 24 39-34 23-18 44-44 26-20 58-33 20-27-54-38-26-11-50-42-19z'/%3E%3Cpath d='M288 319l41 25 23 48-8 62-29 82-26 20-16-73-26-59 9-69z'/%3E%3Cpath d='M495 123l36-32 48 5 26 25 37-8 27 21-13 28-45 9-23 31-55-4-36-29z'/%3E%3Cpath d='M550 211l75-19 54 31 16 70-28 111-51 73-39-40-21-76-42-57 7-59z'/%3E%3Cpath d='M650 137l74-39 105-12 90 30 103 1 84 53-23 42-79 7-28 45-69 1-48 39-67-21-34-54-70-23z'/%3E%3Cpath d='M963 380l58-33 74 20 34 58-41 48-79-7-38-39z'/%3E%3Cpath d='M1097 500l26-13 18 18-22 19z'/%3E%3C/g%3E%3Cg fill='%23F0C75E' fill-opacity='.75'%3E%3Ccircle cx='178' cy='174' r='4'/%3E%3Ccircle cx='583' cy='162' r='4'/%3E%3Ccircle cx='636' cy='276' r='4'/%3E%3Ccircle cx='819' cy='172' r='4'/%3E%3Ccircle cx='1031' cy='409' r='4'/%3E%3C/g%3E%3C/svg%3E");
  background-repeat:no-repeat,repeat-x;
  background-position:center,0 center;
  background-size:100% 100%,1200px 600px;
  animation:worldMapMove 80s linear infinite;
}
@keyframes worldMapMove{
  from{background-position:center,0 center}
  to{background-position:center,-1200px center}
}
@media(max-width:768px){body::before{background-size:cover,900px 450px;animation-duration:65s}}
@media(prefers-reduced-motion:reduce){body::before{animation:none}}

/* Photorealistic animated Earth-at-night background */
body::before{
  opacity:1;
  background:
    linear-gradient(rgba(0,0,0,.42),rgba(0,0,0,.64)),
    url("{{ asset('images/world-night.png') }}") center center / cover no-repeat;
  animation:earthBackgroundMove 25s ease-in-out infinite alternate;
  transform:scale(1.05);
  will-change:transform;
}
@keyframes earthBackgroundMove{
  from{transform:scale(1.05) translate3d(0,0,0)}
  to{transform:scale(1.13) translate3d(-1.5%,-1%,0)}
}
@media(prefers-reduced-motion:reduce){body::before{animation:none;transform:scale(1.05)}}

.topbar,.site-header,main,.site-footer,.mobile-nav{position:relative;z-index:1}

/* ── Top bar ─────────────────────────────────────────────── */
.topbar{background:var(--surface);border-bottom:1px solid var(--border);color:var(--text-muted);font-size:12px;padding:7px 0}
.topbar .container{display:flex;justify-content:space-between;align-items:center}
.topbar-date{display:flex;align-items:center;gap:6px;font-family:'Inter',sans-serif}
.topbar-social{display:flex;gap:14px}
.topbar-social a{color:rgba(255,255,255,.4);font-size:13px;transition:.2s}
.topbar-social a:hover{color:var(--gold)}

/* ── Header ─────────────────────────────────────────────── */
.site-header{background:var(--surface);border-bottom:1px solid var(--border);position:sticky;top:0;z-index:200;box-shadow:0 4px 24px rgba(0,0,0,.4)}
.header-inner{padding:14px 0;display:flex;align-items:center;justify-content:space-between;gap:20px}
.site-brand{display:flex;align-items:center;gap:14px;flex-shrink:0}
.brand-badge{width:48px;height:48px;background:var(--gold);border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:20px;font-weight:900;color:var(--black);font-family:'Inter',sans-serif}
.brand-name{font-size:20px;font-weight:900;color:var(--white);line-height:1.2}
.brand-tagline{font-size:11px;color:var(--gold);letter-spacing:.5px;opacity:.9}
.search-form{display:flex;gap:0;max-width:340px;flex:1}
.search-input{flex:1;padding:10px 14px;border:1px solid var(--border);font-size:13px;outline:none;background:var(--surface2);color:var(--white);transition:.2s}
html[dir="rtl"] .search-input{border-radius:8px 0 0 8px;font-family:'Cairo',sans-serif;direction:rtl}
html[dir="ltr"] .search-input{border-radius:0 8px 8px 0;font-family:'Inter',sans-serif;direction:ltr}
.search-input::placeholder{color:rgba(255,255,255,.3)}
.search-input:focus{border-color:var(--gold)}
html[dir="rtl"] .search-btn{border-radius:0 8px 8px 0}
html[dir="ltr"] .search-btn{border-radius:8px 0 0 8px}
.search-btn{background:var(--gold);color:var(--black);border:none;padding:10px 16px;cursor:pointer;font-size:14px;transition:.2s}
.search-btn:hover{background:var(--gold-light)}
.header-actions{display:flex;align-items:center;gap:10px;flex-shrink:0}
.btn-cta{display:flex;align-items:center;gap:6px;padding:9px 18px;background:var(--gold);color:var(--black);border-radius:8px;font-size:13px;font-weight:700;transition:.2s}
.btn-cta:hover{background:var(--gold-light)}

/* ── Language switcher ──────────────────────────────────── */
.lang-switcher{display:flex;align-items:center;gap:2px;background:rgba(255,255,255,.06);border:1px solid var(--border);border-radius:20px;padding:3px}
.lang-switcher a{padding:4px 10px;border-radius:14px;font-size:11px;font-weight:700;color:rgba(255,255,255,.45);font-family:'Inter',sans-serif;letter-spacing:.3px;transition:.15s;text-decoration:none}
.lang-switcher a:hover{color:var(--white)}
.lang-switcher a.active{background:var(--gold);color:var(--black)}

/* ── Navigation bar ─────────────────────────────────────── */
.navbar{background:var(--surface2);border-top:1px solid var(--border)}
.navbar .container{display:flex;align-items:center;gap:0}
.nav-links{display:flex;gap:0;list-style:none;margin:0;padding:0}
.nav-links li a{display:block;padding:13px 18px;color:rgba(255,255,255,.65);font-size:13.5px;font-weight:600;transition:.2s;border-bottom:2px solid transparent;white-space:nowrap}
.nav-links li a:hover,.nav-links li a.active{color:var(--gold);border-bottom-color:var(--gold)}
.hamburger{display:none;color:var(--white);font-size:20px;padding:13px;cursor:pointer}
html[dir="rtl"] .hamburger{margin-right:auto}
html[dir="ltr"] .hamburger{margin-left:auto}

/* ── Breaking bar ──────────────────────────────────────── */
.breaking-bar{background:var(--red);color:#fff;padding:9px 0;overflow:hidden}
.breaking-bar .container{display:flex;align-items:center;gap:12px}
.breaking-label{background:rgba(0,0,0,.25);padding:3px 12px;border-radius:4px;font-size:12px;font-weight:700;white-space:nowrap;letter-spacing:.3px}
.breaking-ticker{font-size:13px;white-space:nowrap;overflow:hidden;display:flex;gap:0;flex:1}
.breaking-ticker-inner{display:inline-flex;gap:0;animation:ticker 30s linear infinite}
.breaking-ticker-inner:hover{animation-play-state:paused}
.breaking-item{display:inline-flex;align-items:center;gap:8px;padding:0 20px}
.breaking-dot{width:6px;height:6px;background:rgba(255,255,255,.6);border-radius:50%}
@keyframes ticker{from{transform:translateX(0)}to{transform:translateX(-50%)}}

/* ── Container ──────────────────────────────────────────── */
.container{max-width:1240px;margin:0 auto;padding:0 20px}
.main-content{padding:32px 0}

/* ── Article Cards ───────────────────────────────────────── */
.articles-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:20px}
.articles-grid-4{display:grid;grid-template-columns:repeat(4,1fr);gap:16px}
.article-card{background:var(--surface);border:1px solid var(--border);border-radius:12px;overflow:hidden;transition:.2s;display:flex;flex-direction:column}
.article-card:hover{border-color:rgba(200,154,43,.4);box-shadow:0 8px 32px rgba(0,0,0,.5);transform:translateY(-2px)}
.article-card-img{position:relative;overflow:hidden;height:200px;background:var(--surface2)}
.article-card-img img{width:100%;height:100%;object-fit:cover;transition:.3s}
.article-card:hover .article-card-img img{transform:scale(1.04)}
.article-card-img .badge-overlay{position:absolute;top:10px;display:flex;gap:4px;flex-wrap:wrap}
html[dir="rtl"] .article-card-img .badge-overlay{right:10px}
html[dir="ltr"] .article-card-img .badge-overlay{left:10px}
.article-card-body{padding:16px;flex:1;display:flex;flex-direction:column}
.article-cat{font-size:11px;font-weight:700;color:var(--gold);letter-spacing:.5px;margin-bottom:6px;text-transform:uppercase}
.article-title{font-size:14.5px;font-weight:700;color:var(--white);line-height:1.55;margin-bottom:8px;flex:1}
.article-title a:hover{color:var(--gold)}
.article-meta{display:flex;align-items:center;gap:10px;font-size:11px;color:rgba(255,255,255,.4);flex-wrap:wrap}
.article-meta i{color:var(--gold);opacity:.7}
.badge-breaking{background:var(--red);color:#fff;font-size:10px;font-weight:700;padding:3px 7px;border-radius:4px}
.badge-featured{background:var(--gold);color:var(--black);font-size:10px;font-weight:700;padding:3px 7px;border-radius:4px}

/* ── Featured card ──────────────────────────────────────── */
.article-card-featured{background:var(--surface);border:1px solid var(--border);border-radius:14px;overflow:hidden;transition:.2s}
.article-card-featured:hover{border-color:rgba(200,154,43,.4);box-shadow:0 12px 40px rgba(0,0,0,.5)}
.article-card-featured .card-img{height:340px;overflow:hidden;background:var(--surface2);position:relative}
.article-card-featured .card-img img{width:100%;height:100%;object-fit:cover;transition:.3s}
.article-card-featured:hover .card-img img{transform:scale(1.03)}
.article-card-featured .card-body{padding:22px}
.article-card-featured .card-title{font-size:18px;font-weight:900;line-height:1.5;margin-bottom:8px;color:var(--white)}

/* ── Sidebar ──────────────────────────────────────────────── */
.sidebar-widget{background:var(--surface);border:1px solid var(--border);border-radius:12px;overflow:hidden;margin-bottom:20px}
.widget-header{padding:14px 18px;border-bottom:1px solid var(--border);display:flex;align-items:center;gap:8px;position:relative}
.widget-header::after{content:'';position:absolute;bottom:0;width:50px;height:2px;background:var(--gold)}
html[dir="rtl"] .widget-header::after{right:0}
html[dir="ltr"] .widget-header::after{left:0}
.widget-title{font-size:13.5px;font-weight:900;color:var(--white)}
.widget-title i{color:var(--gold)}
.widget-body{padding:0}
.widget-article{display:flex;gap:10px;padding:12px 16px;border-bottom:1px solid var(--border);transition:.15s}
.widget-article:hover{background:var(--surface2)}
.widget-article:last-child{border-bottom:none}
.widget-article-img{width:68px;height:58px;border-radius:6px;object-fit:cover;background:var(--surface2);flex-shrink:0}
.widget-article-body{flex:1}
.widget-article-title{font-size:12.5px;font-weight:700;color:var(--white);line-height:1.4;margin-bottom:4px}
.widget-article-meta{font-size:11px;color:rgba(255,255,255,.35)}

/* ── Section headers ─────────────────────────────────────── */
.section-header{display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;padding-bottom:12px;border-bottom:1px solid var(--border);position:relative}
.section-header::after{content:'';position:absolute;bottom:-1px;width:56px;height:2px;background:var(--gold)}
html[dir="rtl"] .section-header::after{right:0}
html[dir="ltr"] .section-header::after{left:0}
.section-title{font-size:16px;font-weight:900;color:var(--white)}
.section-title i{color:var(--gold)}
.section-more{font-size:12.5px;color:var(--gold);font-weight:600;display:flex;align-items:center;gap:4px}
.section-more:hover{color:var(--gold-light)}

/* ── Newsletter ──────────────────────────────────────────── */
.newsletter-section{background:var(--surface);border:1px solid var(--border);border-radius:14px;padding:36px;text-align:center;margin:28px 0;position:relative;overflow:hidden}
.newsletter-section::before{content:'';position:absolute;top:-60px;left:50%;transform:translateX(-50%);width:300px;height:300px;background:radial-gradient(circle,rgba(200,154,43,.12),transparent 70%);pointer-events:none}
.newsletter-title{font-size:20px;font-weight:900;margin-bottom:8px;color:var(--white)}
.newsletter-sub{font-size:13px;color:rgba(255,255,255,.5);margin-bottom:22px}
.newsletter-form{display:flex;gap:0;max-width:440px;margin:0 auto}
.newsletter-input{flex:1;border:1px solid var(--border);font-size:13px;outline:none;background:var(--surface2);color:var(--white);padding:12px 16px}
html[dir="rtl"] .newsletter-input{border-radius:8px 0 0 8px;font-family:'Cairo',sans-serif;direction:rtl}
html[dir="ltr"] .newsletter-input{border-radius:0 8px 8px 0;font-family:'Inter',sans-serif;direction:ltr}
.newsletter-input::placeholder{color:rgba(255,255,255,.3)}
.newsletter-input:focus{border-color:var(--gold)}
html[dir="rtl"] .newsletter-btn{border-radius:0 8px 8px 0;font-family:'Cairo',sans-serif}
html[dir="ltr"] .newsletter-btn{border-radius:8px 0 0 8px;font-family:'Inter',sans-serif}
.newsletter-btn{background:var(--gold);color:var(--black);border:none;padding:12px 20px;font-size:13.5px;font-weight:700;cursor:pointer;transition:.2s;white-space:nowrap}
.newsletter-btn:hover{background:var(--gold-light)}

/* ── Footer ─────────────────────────────────────────────── */
.site-footer{background:var(--surface);border-top:1px solid var(--border);margin-top:48px}
.footer-main{padding:44px 0;display:grid;grid-template-columns:2fr 1fr 1fr 1fr;gap:32px}
.footer-brand-name{font-size:17px;font-weight:900;color:var(--white);margin-bottom:10px;display:flex;align-items:center;gap:8px}
.footer-brand-badge{width:32px;height:32px;background:var(--gold);border-radius:6px;display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:900;color:var(--black);font-family:'Inter',sans-serif}
.footer-brand-desc{font-size:12.5px;color:rgba(255,255,255,.45);line-height:1.8}
.footer-title{font-size:13px;font-weight:700;color:var(--white);margin-bottom:16px;padding-bottom:8px;border-bottom:1px solid var(--border);letter-spacing:.3px}
.footer-links{list-style:none;display:flex;flex-direction:column;gap:9px}
.footer-links li a{font-size:12.5px;color:rgba(255,255,255,.45);transition:.2s;display:flex;align-items:center;gap:7px}
.footer-links li a:hover{color:var(--gold)}
.footer-links li a i{font-size:9px;color:rgba(255,255,255,.25)}
.footer-social{display:flex;gap:8px;margin-top:16px;flex-wrap:wrap}
.footer-social a{width:34px;height:34px;border-radius:7px;background:rgba(255,255,255,.06);border:1px solid var(--border);display:flex;align-items:center;justify-content:center;font-size:13px;color:rgba(255,255,255,.5);transition:.2s}
.footer-social a:hover{background:var(--gold);color:var(--black);border-color:var(--gold)}
.footer-bottom{border-top:1px solid var(--border);padding:16px 0;text-align:center;font-size:11.5px;color:rgba(255,255,255,.25)}

/* ── Buttons ──────────────────────────────────────────────── */
.btn{display:inline-flex;align-items:center;gap:6px;padding:10px 20px;border-radius:8px;font-size:13.5px;font-weight:700;cursor:pointer;border:none;transition:.2s}
html[dir="rtl"] .btn{font-family:'Cairo',sans-serif}
html[dir="ltr"] .btn{font-family:'Inter',sans-serif}
.btn-primary{background:var(--gold);color:var(--black)}.btn-primary:hover{background:var(--gold-light)}
.btn-outline{border:1px solid var(--border);color:var(--text-muted);background:transparent}.btn-outline:hover{border-color:var(--gold);color:var(--gold)}

/* ── Alerts ───────────────────────────────────────────────── */
.alert{padding:14px 18px;border-radius:8px;margin-bottom:16px;font-size:13.5px;display:flex;align-items:center;gap:10px}
html[dir="rtl"] .alert-success{background:rgba(39,174,96,.12);color:#6ee7b7;border-right:3px solid #27ae60}
html[dir="ltr"] .alert-success{background:rgba(39,174,96,.12);color:#6ee7b7;border-left:3px solid #27ae60}
html[dir="rtl"] .alert-error{background:rgba(214,40,40,.12);color:#fca5a5;border-right:3px solid #D62828}
html[dir="ltr"] .alert-error{background:rgba(214,40,40,.12);color:#fca5a5;border-left:3px solid #D62828}

/* ── Pagination ──────────────────────────────────────────── */
.pagination{display:flex;gap:4px;justify-content:center;margin-top:24px;flex-wrap:wrap}
.pagination a,.pagination span{padding:8px 14px;border-radius:8px;text-decoration:none;font-size:13px;border:1px solid var(--border);color:rgba(255,255,255,.5);background:var(--surface);display:flex;align-items:center;justify-content:center;transition:.2s}
.pagination a:hover{border-color:var(--gold);color:var(--gold)}
.pagination .active span,.pagination span.active{background:var(--gold);border-color:var(--gold);color:var(--black);font-weight:700}

/* ── Tags ──────────────────────────────────────────────────── */
.tags-cloud{display:flex;flex-wrap:wrap;gap:6px}
.tag-chip{padding:5px 12px;background:var(--surface2);border:1px solid var(--border);border-radius:20px;font-size:12px;color:rgba(255,255,255,.5);transition:.2s}
.tag-chip:hover{background:rgba(200,154,43,.15);border-color:var(--gold);color:var(--gold)}

/* ── Homepage layout grids (made responsive via classes) ─── */
.hero-grid{display:grid;grid-template-columns:1.8fr 1fr;gap:20px;align-items:start}
.main-grid{display:grid;grid-template-columns:1fr 310px;gap:28px;align-items:start}
.editor-picks-grid{display:grid;grid-template-columns:1fr 1fr;gap:14px}

/* ── Mobile nav ──────────────────────────────────────────── */
.mobile-nav{display:none;background:var(--surface);position:fixed;top:0;left:0;right:0;bottom:0;z-index:999;overflow-y:auto;padding:24px}
.mobile-nav.open{display:block}
.mobile-nav-close{color:var(--white);font-size:18px;cursor:pointer;margin-bottom:24px;display:flex;align-items:center;gap:8px;padding-bottom:16px;border-bottom:1px solid var(--border)}
.mobile-nav a{display:block;padding:13px 0;color:rgba(255,255,255,.7);font-size:15px;font-weight:600;border-bottom:1px solid var(--border)}
.mobile-nav a:hover{color:var(--gold)}

/* ── Responsive ──────────────────────────────────────────── */
@media(max-width:1024px){.articles-grid{grid-template-columns:repeat(2,1fr)}}
@media(max-width:768px){
  .articles-grid{grid-template-columns:1fr}
  .articles-grid-4{grid-template-columns:1fr 1fr}
  .footer-main{grid-template-columns:1fr 1fr}
  .header-inner{flex-wrap:wrap;gap:10px;padding:10px 0}
  .search-form{order:3;width:100%;max-width:100%}
  .hamburger{display:block}
  .nav-links{display:none}
  .newsletter-form{flex-direction:column}
  .newsletter-input,.newsletter-btn{border-radius:8px!important}
  .topbar .container{flex-wrap:wrap;gap:6px}
  /* Homepage grids → single column */
  .hero-grid{grid-template-columns:1fr!important}
  .main-grid{grid-template-columns:1fr!important}
  .editor-picks-grid{grid-template-columns:1fr!important}
  /* Tighten card images on mobile */
  .card-img{height:220px}
  .article-card-img{height:160px}
  /* Site brand logo */
  .site-brand img{height:44px}
}
@media(max-width:480px){
  .articles-grid-4{grid-template-columns:1fr}
  .footer-main{grid-template-columns:1fr}
  .container{padding:0 14px}
  .section-header{margin-bottom:14px}
  .topbar{display:none}
  .site-brand img{height:38px}
  .header-actions .lang-switcher a{padding:3px 8px;font-size:10px}
}

/* ── Complete responsive layer: phone, tablet, laptop and wide screens ── */
img,video,iframe,svg{max-width:100%}
img{height:auto}
.container,.header-inner,.navbar .container,.breaking-bar .container{width:100%}
.site-brand,.header-actions,.search-form,.breaking-ticker,.widget-article-body{min-width:0}
.site-brand img,.footer-logo{max-width:100%;object-fit:contain}
.article-title,.card-title,.widget-article-title,.footer-links a,.footer-links span{overflow-wrap:anywhere}

@media(min-width:1440px){
  .container{max-width:1360px;padding-inline:28px}
  .main-grid{grid-template-columns:minmax(0,1fr) 340px;gap:34px}
  .articles-grid{gap:24px}
}

@media(min-width:769px) and (max-width:1180px){
  .container{padding-inline:24px}
  .header-inner{gap:14px}
  .search-form{max-width:280px}
  .nav-links li a{padding-inline:11px;font-size:12.5px}
  .main-grid{grid-template-columns:minmax(0,1fr) 280px;gap:22px}
  .articles-grid-4{grid-template-columns:repeat(2,minmax(0,1fr))}
  .footer-main{grid-template-columns:1.5fr 1fr 1fr;gap:26px}
}

@media(max-width:900px){
  .main-grid,.hero-grid{grid-template-columns:1fr!important}
  .header-inner{flex-wrap:wrap}
  .search-form{order:3;flex-basis:100%;max-width:none}
  .navbar .container{justify-content:space-between}
  .nav-links{display:none}
  .hamburger{display:block}
  .article-card-featured .card-img{height:clamp(230px,45vw,360px)}
  .footer-main{grid-template-columns:repeat(2,minmax(0,1fr))}
}

@media(max-width:640px){
  body{font-size:14px}
  .container{padding-inline:14px}
  .main-content{padding:20px 0}
  .topbar{display:none}
  .site-header{position:sticky;top:0}
  .header-inner{padding:9px 0;gap:8px}
  .site-brand{gap:8px;max-width:calc(100% - 52px)}
  .site-brand img{max-width:150px;max-height:44px}
  .brand-badge{width:40px;height:40px;font-size:17px}
  .brand-name{font-size:16px}
  .brand-tagline{font-size:9px}
  .header-actions{gap:6px}
  .btn-cta{padding:8px 10px;font-size:11px}
  .lang-switcher{display:none}
  .search-input,.search-btn{min-height:42px}
  .breaking-bar{padding:7px 0}
  .breaking-bar .container{gap:8px}
  .breaking-label{padding:3px 8px;font-size:10px}
  .breaking-ticker,.breaking-item{font-size:11px}
  .breaking-item{padding-inline:12px}
  .articles-grid,.articles-grid-4,.editor-picks-grid{grid-template-columns:1fr!important;gap:14px}
  .article-card-img{height:clamp(190px,58vw,280px)}
  .article-card-body{padding:14px}
  .article-card-featured .card-img{height:clamp(210px,62vw,320px)}
  .article-card-featured .card-body{padding:16px}
  .article-card-featured .card-title{font-size:16px}
  .section-header{gap:10px;align-items:flex-start}
  .section-title{font-size:15px}
  .section-more{font-size:11px;white-space:nowrap}
  .widget-article{padding:11px 12px}
  .newsletter-section{padding:24px 14px;margin:20px 0}
  .newsletter-title{font-size:17px}
  .newsletter-form{flex-direction:column;gap:9px}
  .newsletter-input,.newsletter-btn{width:100%;border-radius:8px!important}
  .footer-main{grid-template-columns:1fr;padding:30px 0;gap:24px}
  .site-footer{margin-top:32px}
  .mobile-nav{padding:20px 18px;padding-bottom:max(24px,env(safe-area-inset-bottom))}
  .pagination a,.pagination span{padding:7px 11px;font-size:12px}
}

@media(max-width:380px){
  .container{padding-inline:10px}
  .site-brand img{max-width:125px}
  .btn-cta{display:none}
  .article-card-img{height:180px}
  .widget-article-img{width:60px;height:54px}
}

@media(hover:none){
  .article-card:hover,.article-card-featured:hover{transform:none}
}

@media(prefers-reduced-motion:reduce){
  *,*::before,*::after{scroll-behavior:auto!important;animation-duration:.01ms!important;animation-iteration-count:1!important;transition-duration:.01ms!important}
}



/* ── Desktop categories dropdown ─────────────────────────── */

.nav-links > li {
    position: relative;
}

.nav-category-item {
    position: relative;
}

.nav-category-parent {
    display: flex !important;
    align-items: center;
    gap: 7px;
}

.nav-category-arrow {
    color: rgba(255, 255, 255, 0.35);
    font-size: 9px;
    transition: transform 0.2s ease, color 0.2s ease;
}

.nav-category-item:hover .nav-category-arrow,
.nav-category-item:focus-within .nav-category-arrow {
    color: var(--gold);
    transform: rotate(180deg);
}

.nav-category-dropdown {
    position: absolute;
    top: calc(100% + 1px);
    z-index: 500;
    display: block;
    min-width: 220px;
    padding: 8px;
    visibility: hidden;
    opacity: 0;
    border: 1px solid var(--border);
    border-radius: 0 0 10px 10px;
    background: var(--surface);
    box-shadow: 0 14px 35px rgba(0, 0, 0, 0.55);
    transform: translateY(8px);
    transition:
        opacity 0.2s ease,
        visibility 0.2s ease,
        transform 0.2s ease;
}

html[dir="rtl"] .nav-category-dropdown {
    right: 0;
}

html[dir="ltr"] .nav-category-dropdown {
    left: 0;
}

.nav-category-item:hover > .nav-category-dropdown,
.nav-category-item:focus-within > .nav-category-dropdown {
    visibility: visible;
    opacity: 1;
    transform: translateY(0);
}

.nav-category-dropdown a {
    display: flex !important;
    align-items: center;
    justify-content: space-between;
    gap: 14px;
    width: 100%;
    padding: 10px 12px !important;
    color: rgba(255, 255, 255, 0.62) !important;
    border: 0 !important;
    border-radius: 7px;
    font-size: 13px !important;
    white-space: nowrap;
    transition:
        color 0.2s ease,
        background-color 0.2s ease;
}

.nav-category-dropdown a:hover,
.nav-category-dropdown a.active {
    color: var(--gold) !important;
    background: rgba(200, 154, 43, 0.1);
}

.nav-category-dropdown a i {
    color: rgba(200, 154, 43, 0.55);
    font-size: 9px;
}



/* ── Mobile categories dropdown ──────────────────────────── */

.mobile-category-group {
    border-bottom: 1px solid var(--border);
}

.mobile-category-group summary {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    padding: 13px 0;
    color: rgba(255, 255, 255, 0.7);
    font-size: 15px;
    font-weight: 600;
    list-style: none;
    cursor: pointer;
}

.mobile-category-group summary::-webkit-details-marker {
    display: none;
}

.mobile-category-group summary i {
    color: rgba(255, 255, 255, 0.35);
    font-size: 10px;
    transition: transform 0.2s ease;
}

.mobile-category-group[open] summary {
    color: var(--gold);
}

.mobile-category-group[open] summary i {
    color: var(--gold);
    transform: rotate(180deg);
}

.mobile-category-children {
    padding: 0 12px 10px;
}

.mobile-category-children a {
    display: flex;
    align-items: center;
    gap: 9px;
    padding: 10px 12px;
    color: rgba(255, 255, 255, 0.48);
    border-bottom: 0;
    border-radius: 6px;
    font-size: 13px;
}

.mobile-category-children a:hover,
.mobile-category-children a.active {
    color: var(--gold);
    background: rgba(200, 154, 43, 0.08);
}

.mobile-category-children a i {
    color: rgba(200, 154, 43, 0.55);
    font-size: 9px;
}

.mobile-category-children .mobile-parent-page {
    margin-bottom: 3px;
    color: rgba(255, 255, 255, 0.68);
    background: rgba(255, 255, 255, 0.035);
}
</style>
@stack('styles')
</head>
<body>
<!-- Top info bar -->
<div class="topbar">
  <div class="container">
    <div class="topbar-date">
      <i class="fa-regular fa-calendar"></i>
      <span>{{ now()->locale($locale)->translatedFormat('l، d F Y') }}</span>
    </div>
    <div class="topbar-social">
        @foreach ($socialLinks as $key => $social)
            @if (filled($siteSettings[$key] ?? null))
                <a
                    href="{{ $siteSettings[$key] }}"
                    target="_blank"
                    rel="noopener noreferrer"
                    aria-label="{{ $social['label'] }}"
                    title="{{ $social['label'] }}"
                >
                    <i class="{{ $social['icon'] }}"></i>
                </a>
            @endif
        @endforeach
    </div>
  </div>
</div>

<!-- Header -->
<header class="site-header">
  <div class="container">
    <div class="header-inner">
      <a href="{{ route('home') }}" class="site-brand">
      @include('partials.site-logo', [
    'class' => 'site-logo',
    'style' => 'max-width:180px;max-height:70px;object-fit:contain'
])
      </a>
      <form action="{{ route('search') }}" method="GET" class="search-form">
        <input type="text" name="q" class="search-input" placeholder="{{ __('messages.search_placeholder') }}" value="{{ request('q') }}">
        <button type="submit" class="search-btn"><i class="fa-solid fa-search"></i></button>
      </form>
      <div class="header-actions">
        <div class="lang-switcher">
          <a href="{{ route('language.switch','ar') }}" class="{{ $locale==='ar'?'active':'' }}">ع</a>
          <a href="{{ route('language.switch','en') }}" class="{{ $locale==='en'?'active':'' }}">EN</a>
          <a href="{{ route('language.switch','fr') }}" class="{{ $locale==='fr'?'active':'' }}">FR</a>
        </div>
        @auth
        {{-- <a href="{{ route('admin.dashboard') }}" class="btn-cta"><i class="fa-solid fa-gauge-high"></i> {{ __('messages.btn_dashboard') }}</a> --}}
        @else
        {{-- <a href="{{ route('login') }}" class="btn-cta"><i class="fa-solid fa-right-to-bracket"></i> {{ __('messages.btn_login') }}</a> --}}
        @endauth

      </div>
    </div>
  </div>
  <nav class="navbar">
    <div class="container">
      <button class="hamburger" onclick="document.getElementById('mobileNav').classList.toggle('open')">
        <i class="fa-solid fa-bars"></i>
      </button>
      <ul class="nav-links">
        <li><a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}">{{ __('messages.nav_home') }}</a></li>
      @isset($navCategories)
    @foreach ($navCategories as $navCat)
        @php
            $hasChildren = $navCat->children->isNotEmpty();

            $isParentActive =
                request()->routeIs('categories.show') &&
                (
                    request()->route('slug') === $navCat->slug ||
                    $navCat->children->contains(
                        'slug',
                        request()->route('slug')
                    )
                );
        @endphp

        <li class="nav-category-item">
            <a
                href="{{ route('categories.show', $navCat->slug) }}"
                class="nav-category-parent {{ $isParentActive ? 'active' : '' }}"
            >
                <span>{{ $navCat->name }}</span>

                @if ($hasChildren)
                    <i class="fa-solid fa-chevron-down nav-category-arrow"></i>
                @endif
            </a>

            @if ($hasChildren)
                <div class="nav-category-dropdown">
                    @foreach ($navCat->children as $child)
                        <a
                            href="{{ route('categories.show', $child->slug) }}"
                            class="{{
                                request()->routeIs('categories.show') &&
                                request()->route('slug') === $child->slug
                                    ? 'active'
                                    : ''
                            }}"
                        >
                            <span>{{ $child->name }}</span>

                            <i class="fa-solid fa-angle-{{ $isRtl ? 'left' : 'right' }}"></i>
                        </a>
                    @endforeach
                </div>
            @endif
        </li>
    @endforeach
@endisset
        <li><a href="{{ route('videos.index') }}" class="{{ request()->routeIs('videos.*') ? 'active' : '' }}">{{ __('messages.nav_videos') }}</a></li>
        <li>
          <a href="{{ route('live') }}" class="{{ request()->routeIs('live') ? 'active' : '' }}" style="display:flex;align-items:center;gap:6px">
            {{ __('messages.nav_live') }}
            @isset($activeStream)
            <span style="display:inline-flex;align-items:center;gap:4px;background:var(--red);color:#fff;font-size:10px;font-weight:700;padding:2px 7px;border-radius:10px;letter-spacing:.3px;animation:navLivePulse 1.8s ease infinite">
              <span style="width:5px;height:5px;border-radius:50%;background:#fff;display:inline-block"></span>{{ __('messages.live_badge') }}
            </span>
            @endisset
          </a>
        </li>

        {{-- <li>
          <a href="{{ route('markets.index') }}" class="{{ request()->routeIs('markets.*') ? 'active' : '' }}">
            <i class="fa-solid fa-chart-line"></i> {{ __('markets.nav') }}
          </a>
        </li> --}}

        <li>

<a
    href="{{ route('about') }}"
    class="{{ request()->routeIs('about') ? 'active' : '' }}"
>
    {{ __('messages.nav_about') }}
</a>
        </li>


        <li><a href="{{ route('contact') }}" class="{{ request()->routeIs('contact*') ? 'active' : '' }}">{{ __('messages.nav_contact') }}</a></li>
      </ul>
    </div>
  </nav>
  <style>@keyframes navLivePulse{0%,100%{opacity:1}50%{opacity:.6}}</style>
</header>

<!-- Mobile Nav -->
<div class="mobile-nav" id="mobileNav">
  <div class="mobile-nav-close" onclick="document.getElementById('mobileNav').classList.remove('open')">
    <i class="fa-solid fa-times"></i> {{ __('messages.btn_close_menu') }}
  </div>
  <a href="{{ route('home') }}">{{ __('messages.nav_home') }}</a>
  @isset($navCategories)
    @foreach($navCategories as $navCat)
    <a href="{{ route('categories.show', $navCat->slug) }}">{{ $navCat->name }}</a>
    @endforeach
  @endisset
  <a href="{{ route('videos.index') }}">{{ __('messages.nav_videos') }}</a>
  <a href="{{ route('live') }}" style="display:flex;align-items:center;gap:6px">
    {{ __('messages.nav_live') }}
    @isset($activeStream)
    <span style="background:var(--red);color:#fff;font-size:10px;font-weight:700;padding:2px 7px;border-radius:10px">{{ __('messages.live_badge') }}</span>
    @endisset
  </a>
  {{-- <a href="{{ route('markets.index') }}">
    <i class="fa-solid fa-chart-line"></i> {{ __('markets.nav') }}
  </a> --}}
  <a href="{{ route('contact') }}">{{ __('messages.nav_contact') }}</a>
  <div style="display:flex;gap:8px;padding:16px 0;border-top:1px solid var(--border);margin-top:8px">
    <a href="{{ route('language.switch','ar') }}" style="padding:6px 14px;border-radius:6px;font-size:12px;font-weight:700;background:{{ $locale==='ar'?'var(--gold)':'var(--surface2)' }};color:{{ $locale==='ar'?'var(--black)':'rgba(255,255,255,.6)' }}">ع</a>
    <a href="{{ route('language.switch','en') }}" style="padding:6px 14px;border-radius:6px;font-size:12px;font-weight:700;background:{{ $locale==='en'?'var(--gold)':'var(--surface2)' }};color:{{ $locale==='en'?'var(--black)':'rgba(255,255,255,.6)' }}">EN</a>
    <a href="{{ route('language.switch','fr') }}" style="padding:6px 14px;border-radius:6px;font-size:12px;font-weight:700;background:{{ $locale==='fr'?'var(--gold)':'var(--surface2)' }};color:{{ $locale==='fr'?'var(--black)':'rgba(255,255,255,.6)' }}">FR</a>
  </div>
</div>

{{-- Header-position ads --}}
@isset($headerAds)
@if($headerAds->count())
<div style="background:var(--surface2);border-bottom:1px solid var(--border);padding:8px 0">
  <div class="container" style="display:flex;justify-content:center;align-items:center;gap:12px;flex-wrap:wrap">
    @foreach($headerAds as $ad)
    @if($ad->link)<a href="{{ $ad->link }}" target="_blank" rel="noopener noreferrer" style="display:block">@endif
      @if($ad->image)
      <img src="{{ $ad->image }}" alt="{{ $ad->title }}" style="max-height:80px;border-radius:6px;display:block">
      @else
      <div style="background:rgba(200,154,43,.07);border:1px dashed rgba(200,154,43,.25);border-radius:6px;padding:10px 20px;text-align:center">
        <span style="font-size:10px;color:rgba(255,255,255,.3);letter-spacing:.5px;display:block;margin-bottom:3px">Ad</span>
        <span style="color:rgba(255,255,255,.65);font-size:13px;font-weight:700">{{ $ad->title }}</span>
      </div>
      @endif
    @if($ad->link)</a>@endif
    @endforeach
  </div>
</div>
@endif
@endisset

<main>
  @if(session('success'))
  <div class="container" style="padding-top:16px">
    <div class="alert alert-success"><i class="fa-solid fa-circle-check"></i> {{ session('success') }}</div>
  </div>
  @endif
  @yield('content')
</main>

@section('newsletter_section')
<div class="container">
  <div class="newsletter-section">
    <i class="fa-solid fa-bell" style="font-size:28px;color:var(--gold);margin-bottom:12px;display:block"></i>
    <div class="newsletter-title">{{ __('messages.newsletter_title') }}</div>
    <div class="newsletter-sub">{{ __('messages.newsletter_sub') }}</div>
    <form action="{{ route('newsletter.subscribe') }}" method="POST" class="newsletter-form">
      @csrf
      <input type="email" name="email" class="newsletter-input" placeholder="{{ __('messages.newsletter_placeholder') }}" required>
      <button type="submit" class="newsletter-btn"><i class="fa-solid fa-paper-plane"></i> {{ __('messages.newsletter_btn') }}</button>
    </form>
  </div>
</div>
@show

{{-- Footer-position ads --}}
@isset($footerAds)
@if($footerAds->count())
<div style="padding:12px 0;border-top:1px solid var(--border);background:var(--surface2)">
  <div class="container" style="display:flex;justify-content:center;align-items:center;gap:12px;flex-wrap:wrap">
    @foreach($footerAds as $ad)
    @if($ad->link)<a href="{{ $ad->link }}" target="_blank" rel="noopener noreferrer" style="display:block">@endif
      @if($ad->image)
      <img src="{{ $ad->image }}" alt="{{ $ad->title }}" style="max-height:80px;border-radius:6px;display:block">
      @else
      <div style="background:rgba(200,154,43,.07);border:1px dashed rgba(200,154,43,.25);border-radius:6px;padding:10px 20px;text-align:center">
        <span style="font-size:10px;color:rgba(255,255,255,.3);letter-spacing:.5px;display:block;margin-bottom:3px">Ad</span>
        <span style="color:rgba(255,255,255,.65);font-size:13px;font-weight:700">{{ $ad->title }}</span>
      </div>
      @endif
    @if($ad->link)</a>@endif
    @endforeach
  </div>
</div>
@endif
@endisset


<footer class="site-footer">
  <div class="container">
    <div class="footer-main">

      {{-- Brand + description + social --}}
      <div>
        <div class="footer-brand-name">
         @include('partials.site-logo', [
    'class' => 'footer-logo',
    'style' => 'max-width:180px;max-height:70px;object-fit:contain'
])
          {{ $siteSettings['site_name'] ?? __('messages.site_name') }}
        </div>
        <p class="footer-brand-desc">
          {{ $siteSettings['footer_text'] ?? ($siteSettings['site_tagline'] ?? __('messages.site_tagline')) }}
        </p>
        <div class="footer-social">
          @foreach ($socialLinks as $key => $social)
            @if (filled($siteSettings[$key] ?? null))
              <a
                href="{{ $siteSettings[$key] }}"
                target="_blank"
                rel="noopener noreferrer"
                aria-label="{{ $social['label'] }}"
                title="{{ $social['label'] }}"
              >
                <i class="{{ $social['icon'] }}"></i>
              </a>
            @endif
          @endforeach
        </div>
      </div>

      {{-- Quick links --}}
      <div>
        <div class="footer-title">{{ $isRtl ? 'روابط سريعة' : 'Quick Links' }}</div>
        <ul class="footer-links">
          <li><a href="{{ route('home') }}"><i class="fa-solid fa-chevron-{{ $isRtl ? 'left' : 'right' }}"></i>{{ __('messages.nav_home') }}</a></li>
          <li><a href="{{ route('videos.index') }}"><i class="fa-solid fa-chevron-{{ $isRtl ? 'left' : 'right' }}"></i>{{ __('messages.nav_videos') }}</a></li>
          <li><a href="{{ route('live') }}"><i class="fa-solid fa-chevron-{{ $isRtl ? 'left' : 'right' }}"></i>{{ __('messages.nav_live') }}</a></li>
          {{-- <li><a href="{{ route('markets.index') }}"><i class="fa-solid fa-chevron-{{ $isRtl ? 'left' : 'right' }}"></i>{{ __('markets.nav') }}</a></li> --}}
          <li><a href="{{ route('contact') }}"><i class="fa-solid fa-chevron-{{ $isRtl ? 'left' : 'right' }}"></i>{{ __('messages.nav_contact') }}</a></li>
        </ul>
      </div>

      {{-- Categories --}}
      @isset($navCategories)
      <div>
        <div class="footer-title">{{ __('messages.all_categories') }}</div>
        <ul class="footer-links">
          @foreach($navCategories->take(5) as $navCat)
          <li>
            <a href="{{ route('categories.show', $navCat->slug) }}">
              <i class="fa-solid fa-chevron-{{ $isRtl ? 'left' : 'right' }}"></i>{{ $navCat->name }}
            </a>
          </li>
          @endforeach
        </ul>
      </div>
      @endisset

      {{-- Contact info --}}
      <div>
        <div class="footer-title">{{ __('messages.nav_contact') }}</div>
        <ul class="footer-links">
          @if(filled($siteSettings['site_email'] ?? null))
          <li><a href="mailto:{{ $siteSettings['site_email'] }}"><i class="fa-solid fa-envelope"></i>{{ $siteSettings['site_email'] }}</a></li>
          @endif
          @if(filled($siteSettings['site_phone'] ?? null))
          <li><a href="tel:{{ $siteSettings['site_phone'] }}"><i class="fa-solid fa-phone"></i>{{ $siteSettings['site_phone'] }}</a></li>
          @endif
          @if(filled($siteSettings['site_address'] ?? null))
          <li><span><i class="fa-solid fa-location-dot"></i>{{ $siteSettings['site_address'] }}</span></li>
          @endif
        </ul>
      </div>

    </div>

    <div class="footer-bottom">
      {{ $siteSettings['footer_text'] ?? ('© ' . now()->year . ' ' . ($siteSettings['site_name'] ?? __('messages.site_name'))) }}
    </div>
  </div>
</footer>

@stack('scripts')
</body>
</html>