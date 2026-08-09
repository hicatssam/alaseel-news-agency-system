@extends('layouts.app')

@section('title', __('markets.page_title'))
@section('description', __('markets.meta_description'))

@php
    $fx = $marketData['fx'];
    $metals = $marketData['metals'];
    $locale = app()->getLocale();
    $isRtl = $locale === 'ar';
    $tvLocale = match ($locale) {
        'ar' => 'ar_AE',
        'fr' => 'fr',
        default => 'en',
    };
    $currencySymbols = [
        'USD' => '$',
        'EUR' => '€',
        'GBP' => '£',
        'ILS' => '₪',
        'JOD' => 'د.أ',
        'SAR' => 'ر.س',
        'AED' => 'د.إ',
        'EGP' => 'ج.م',
        'TRY' => '₺',
        'KWD' => 'د.ك',
        'QAR' => 'ر.ق',
        'JPY' => '¥',
        'CHF' => 'Fr',
        'CAD' => 'CA$',
        'AUD' => 'A$',
        'CNY' => '¥',
    ];
    $metalsUpdatedAt = collect($metals['items'] ?? [])
        ->pluck('updated_at')
        ->filter()
        ->max();
@endphp

@push('styles')
<style>
.markets-page{padding:36px 0 12px}
.markets-hero{position:relative;overflow:hidden;padding:34px;border:1px solid rgba(200,154,43,.32);border-radius:18px;background:linear-gradient(135deg,rgba(200,154,43,.16),rgba(21,21,21,.96) 52%,rgba(11,11,11,.96));box-shadow:0 18px 50px rgba(0,0,0,.35);margin-bottom:26px}
.markets-hero::after{content:'\f201';position:absolute;bottom:-38px;color:rgba(200,154,43,.08);font-family:'Font Awesome 6 Free';font-size:190px;font-weight:900;line-height:1}
html[dir="rtl"] .markets-hero::after{left:22px}
html[dir="ltr"] .markets-hero::after{right:22px}
.markets-eyebrow{display:inline-flex;align-items:center;gap:7px;margin-bottom:10px;color:var(--gold);font-size:12px;font-weight:800;letter-spacing:.4px}
.markets-title{max-width:760px;color:var(--white);font-size:30px;font-weight:900;line-height:1.35;margin-bottom:10px}
.markets-subtitle{max-width:780px;color:rgba(255,255,255,.58);font-size:14px;line-height:1.9}
.markets-status-row{display:flex;align-items:center;gap:8px;flex-wrap:wrap;margin-top:18px}
.markets-status{display:inline-flex;align-items:center;gap:6px;padding:6px 11px;border:1px solid var(--border);border-radius:999px;background:rgba(255,255,255,.04);color:rgba(255,255,255,.58);font-size:11px}
.markets-status i{color:var(--gold)}
.markets-status.stale{border-color:rgba(243,156,18,.35);color:#f8c471;background:rgba(243,156,18,.08)}
.markets-alert{display:flex;align-items:flex-start;gap:10px;padding:15px 17px;border:1px solid rgba(214,40,40,.35);border-radius:10px;background:rgba(214,40,40,.1);color:#fca5a5;font-size:13px;line-height:1.7;margin-bottom:22px}
.markets-section{margin-bottom:30px}
.markets-section-heading{display:flex;align-items:flex-end;justify-content:space-between;gap:18px;margin-bottom:15px;padding-bottom:12px;border-bottom:1px solid var(--border);position:relative}
.markets-section-heading::after{content:'';position:absolute;bottom:-1px;width:70px;height:2px;background:var(--gold)}
html[dir="rtl"] .markets-section-heading::after{right:0}
html[dir="ltr"] .markets-section-heading::after{left:0}
.markets-section-title{display:flex;align-items:center;gap:9px;color:var(--white);font-size:18px;font-weight:900}
.markets-section-title i{color:var(--gold)}
.markets-section-note{color:rgba(255,255,255,.4);font-size:11.5px}
.currency-grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:12px}
.currency-card{padding:17px;border:1px solid var(--border);border-radius:12px;background:rgba(21,21,21,.92);transition:.2s}
.currency-card:hover{transform:translateY(-2px);border-color:rgba(200,154,43,.45);box-shadow:0 12px 28px rgba(0,0,0,.25)}
.currency-card-head{display:flex;align-items:center;justify-content:space-between;gap:10px;margin-bottom:12px}
.currency-code{display:inline-flex;align-items:center;justify-content:center;min-width:42px;height:26px;padding:0 8px;border-radius:6px;background:rgba(200,154,43,.13);color:var(--gold);font-family:'Inter',sans-serif;font-size:11px;font-weight:800}
.currency-symbol{color:rgba(255,255,255,.25);font-size:18px;font-weight:700}
.currency-name{color:rgba(255,255,255,.58);font-size:11.5px;margin-bottom:5px}
.currency-rate{direction:ltr;text-align:start;color:var(--white);font-family:'Inter',sans-serif;font-size:18px;font-weight:800}
.currency-base{margin-top:5px;color:rgba(255,255,255,.32);font-size:10.5px}
.market-panels{display:grid;grid-template-columns:1.25fr .75fr;gap:18px;align-items:start}
.market-panel{overflow:hidden;border:1px solid var(--border);border-radius:14px;background:rgba(21,21,21,.94)}
.market-panel-head{display:flex;align-items:center;justify-content:space-between;gap:12px;padding:15px 18px;border-bottom:1px solid var(--border);background:rgba(255,255,255,.02)}
.market-panel-title{display:flex;align-items:center;gap:8px;color:var(--white);font-size:14px;font-weight:800}
.market-panel-title i{color:var(--gold)}
.market-panel-body{padding:18px}
.converter-grid{display:grid;grid-template-columns:1fr 1fr;gap:12px}
.converter-field label{display:block;margin-bottom:6px;color:rgba(255,255,255,.5);font-size:11.5px;font-weight:700}
.converter-control{width:100%;height:44px;padding:0 12px;border:1px solid var(--border);border-radius:8px;outline:none;background:var(--surface2);color:var(--white);font-family:inherit;transition:.2s}
.converter-control:focus{border-color:var(--gold);box-shadow:0 0 0 3px rgba(200,154,43,.1)}
.converter-result{display:flex;align-items:center;justify-content:center;min-height:92px;margin-top:14px;padding:17px;border:1px solid rgba(200,154,43,.24);border-radius:10px;background:rgba(200,154,43,.08);text-align:center}
.converter-result-label{color:rgba(255,255,255,.43);font-size:11.5px;margin-bottom:5px}
.converter-result-value{direction:ltr;color:var(--gold-light);font-family:'Inter',sans-serif;font-size:24px;font-weight:800}
.provider-list{display:flex;flex-direction:column;gap:10px}
.provider-link{display:flex;align-items:center;justify-content:space-between;gap:12px;padding:11px 12px;border:1px solid var(--border);border-radius:8px;color:rgba(255,255,255,.58);font-size:12px;transition:.2s}
.provider-link:hover{border-color:rgba(200,154,43,.4);color:var(--gold)}
.metal-overview{display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px}
.metal-card{padding:20px;border:1px solid var(--border);border-radius:14px;background:linear-gradient(145deg,rgba(200,154,43,.1),rgba(21,21,21,.95) 58%)}
.metal-card.silver{background:linear-gradient(145deg,rgba(210,218,226,.1),rgba(21,21,21,.95) 58%)}
.metal-card-top{display:flex;align-items:center;justify-content:space-between;gap:12px;margin-bottom:16px}
.metal-name{display:flex;align-items:center;gap:9px;color:var(--white);font-size:16px;font-weight:900}
.metal-name i{color:var(--gold);font-size:20px}
.metal-card.silver .metal-name i{color:#d2dae2}
.metal-symbol{padding:5px 9px;border-radius:6px;background:rgba(255,255,255,.06);color:rgba(255,255,255,.45);font-family:'Inter',sans-serif;font-size:10px;font-weight:700}
.metal-prices{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:9px}
.metal-price{padding:10px;border:1px solid rgba(255,255,255,.07);border-radius:8px;background:rgba(0,0,0,.18)}
.metal-price-code{color:rgba(255,255,255,.36);font-family:'Inter',sans-serif;font-size:10px;margin-bottom:4px}
.metal-price-value{direction:ltr;color:var(--white);font-family:'Inter',sans-serif;font-size:15px;font-weight:800}
.market-table-wrap{overflow-x:auto;border:1px solid var(--border);border-radius:14px;background:rgba(21,21,21,.94)}
.market-table{width:100%;border-collapse:collapse;min-width:660px}
.market-table th,.market-table td{padding:14px 16px;border-bottom:1px solid var(--border);text-align:start;font-size:12.5px}
.market-table th{background:rgba(255,255,255,.025);color:rgba(255,255,255,.45);font-size:11px;font-weight:700;white-space:nowrap}
.market-table tr:last-child td{border-bottom:0}
.karat-label{display:inline-flex;align-items:center;gap:7px;color:var(--white);font-weight:800}
.karat-dot{width:8px;height:8px;border-radius:50%;background:var(--gold);box-shadow:0 0 0 4px rgba(200,154,43,.12)}
.price-cell{direction:ltr;color:var(--white);font-family:'Inter',sans-serif;font-weight:700;white-space:nowrap}
.tradingview-shell{overflow:hidden;min-height:610px;border:1px solid var(--border);border-radius:14px;background:#111}
.market-disclaimer{display:flex;gap:10px;margin-top:16px;padding:14px 16px;border:1px solid rgba(200,154,43,.2);border-radius:10px;background:rgba(200,154,43,.06);color:rgba(255,255,255,.48);font-size:11.5px;line-height:1.8}
.market-disclaimer i{color:var(--gold);margin-top:3px}
@media(max-width:1024px){.currency-grid{grid-template-columns:repeat(3,minmax(0,1fr))}.market-panels{grid-template-columns:1fr}.tradingview-shell{min-height:640px}}
@media(max-width:768px){.markets-page{padding-top:22px}.markets-hero{padding:24px 20px}.markets-title{font-size:24px}.markets-section-heading{align-items:flex-start;flex-direction:column;gap:5px}.currency-grid{grid-template-columns:repeat(2,minmax(0,1fr))}.metal-overview{grid-template-columns:1fr}.converter-grid{grid-template-columns:1fr}.tradingview-shell{min-height:680px}}
@media(max-width:480px){.currency-grid{grid-template-columns:1fr}.metal-prices{grid-template-columns:1fr 1fr}.markets-hero::after{font-size:130px}}
</style>
@endpush

@section('content')
<div class="markets-page">
    <div class="container">
        <section class="markets-hero">
            <div class="markets-eyebrow">
                <i class="fa-solid fa-chart-column"></i>
                {{ __('markets.eyebrow') }}
            </div>
            <h1 class="markets-title">{{ __('markets.hero_title') }}</h1>
            <p class="markets-subtitle">{{ __('markets.hero_subtitle') }}</p>

            <div class="markets-status-row">
                @if($fx['available'])
                    <span class="markets-status {{ $fx['stale'] ? 'stale' : '' }}">
                        <i class="fa-regular fa-clock"></i>
                        {{ $fx['stale'] ? __('markets.cached_data') : __('markets.fx_date') }}:
                        {{ $fx['date'] ?? __('markets.not_available') }}
                    </span>
                @endif

                @if($metals['available'])
                    <span class="markets-status {{ $metals['stale'] ? 'stale' : '' }}">
                        <i class="fa-solid fa-coins"></i>
                        {{ $metals['stale'] ? __('markets.cached_data') : __('markets.metals_live') }}:
                        {{ $metalsUpdatedAt
                            ? \Illuminate\Support\Carbon::parse($metalsUpdatedAt)->locale($locale)->translatedFormat('d M Y H:i')
                            : __('markets.not_available') }}
                    </span>
                @endif
            </div>
        </section>

        @if(! $marketData['has_any_data'])
            <div class="markets-alert">
                <i class="fa-solid fa-triangle-exclamation"></i>
                <div>
                    <strong>{{ __('markets.data_unavailable') }}</strong><br>
                    {{ __('markets.try_later') }}
                </div>
            </div>
        @endif

        <section class="markets-section">
            <div class="markets-section-heading">
                <h2 class="markets-section-title">
                    <i class="fa-solid fa-money-bill-transfer"></i>
                    {{ __('markets.exchange_rates') }}
                </h2>
                <span class="markets-section-note">{{ __('markets.fx_reference_note', ['base' => $fx['base']]) }}</span>
            </div>

            @if($fx['available'])
                <div class="currency-grid">
                    @foreach($fx['rates'] as $currency => $rate)
                        @continue($currency === $fx['base'])
                        <article class="currency-card">
                            <div class="currency-card-head">
                                <span class="currency-code">{{ $currency }}</span>
                                <span class="currency-symbol">{{ $currencySymbols[$currency] ?? $currency }}</span>
                            </div>
                            <div class="currency-name">{{ __('markets.currencies.' . $currency) }}</div>
                            <div class="currency-rate">
                                {{ number_format((float) $rate, (float) $rate >= 100 ? 2 : 4) }}
                            </div>
                            <div class="currency-base">1 {{ $fx['base'] }} = {{ $currency }}</div>
                        </article>
                    @endforeach
                </div>

                <div class="market-panels" style="margin-top:18px">
                    <div class="market-panel">
                        <div class="market-panel-head">
                            <div class="market-panel-title">
                                <i class="fa-solid fa-calculator"></i>
                                {{ __('markets.converter') }}
                            </div>
                        </div>
                        <div class="market-panel-body">
                            <div class="converter-grid">
                                <div class="converter-field">
                                    <label for="marketAmount">{{ __('markets.amount') }}</label>
                                    <input id="marketAmount" class="converter-control" type="number" min="0" step="any" value="1" inputmode="decimal">
                                </div>
                                <div></div>
                                <div class="converter-field">
                                    <label for="marketFrom">{{ __('markets.from_currency') }}</label>
                                    <select id="marketFrom" class="converter-control">
                                        @foreach($fx['rates'] as $currency => $rate)
                                            <option value="{{ $currency }}" @selected($currency === $fx['base'])>
                                                {{ $currency }} — {{ __('markets.currencies.' . $currency) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="converter-field">
                                    <label for="marketTo">{{ __('markets.to_currency') }}</label>
                                    <select id="marketTo" class="converter-control">
                                        @foreach($fx['rates'] as $currency => $rate)
                                            <option value="{{ $currency }}" @selected($currency === 'ILS')>
                                                {{ $currency }} — {{ __('markets.currencies.' . $currency) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="converter-result" aria-live="polite">
                                <div>
                                    <div class="converter-result-label">{{ __('markets.conversion_result') }}</div>
                                    <div class="converter-result-value" id="marketConversionResult">—</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <aside class="market-panel">
                        <div class="market-panel-head">
                            <div class="market-panel-title">
                                <i class="fa-solid fa-circle-info"></i>
                                {{ __('markets.data_sources') }}
                            </div>
                        </div>
                        <div class="market-panel-body">
                            <div class="provider-list">
                                <a class="provider-link" href="https://frankfurter.dev/" target="_blank" rel="noopener noreferrer">
                                    <span>Frankfurter — {{ __('markets.exchange_rates') }}</span>
                                    <i class="fa-solid fa-arrow-up-right-from-square"></i>
                                </a>
                                <a class="provider-link" href="https://gold-api.com/docs" target="_blank" rel="noopener noreferrer">
                                    <span>Gold API — {{ __('markets.metals') }}</span>
                                    <i class="fa-solid fa-arrow-up-right-from-square"></i>
                                </a>
                                <a class="provider-link" href="https://www.tradingview.com/widget-docs/" target="_blank" rel="noopener noreferrer">
                                    <span>TradingView — {{ __('markets.stock_markets') }}</span>
                                    <i class="fa-solid fa-arrow-up-right-from-square"></i>
                                </a>
                            </div>
                        </div>
                    </aside>
                </div>
            @else
                <div class="markets-alert">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                    <span>{{ __('markets.fx_unavailable') }}</span>
                </div>
            @endif
        </section>

        <section class="markets-section">
            <div class="markets-section-heading">
                <h2 class="markets-section-title">
                    <i class="fa-solid fa-gem"></i>
                    {{ __('markets.gold_and_silver') }}
                </h2>
                <span class="markets-section-note">{{ __('markets.troy_ounce_note') }}</span>
            </div>

            @if($metals['available'])
                <div class="metal-overview">
                    @foreach(['XAU' => 'gold', 'XAG' => 'silver'] as $symbol => $type)
                        @if(isset($metals['items'][$symbol]))
                            <article class="metal-card {{ $type === 'silver' ? 'silver' : '' }}">
                                <div class="metal-card-top">
                                    <div class="metal-name">
                                        <i class="fa-solid {{ $type === 'gold' ? 'fa-coins' : 'fa-circle' }}"></i>
                                        {{ __('markets.' . $type) }}
                                    </div>
                                    <span class="metal-symbol">{{ $symbol }} / OZ</span>
                                </div>
                                <div class="metal-prices">
                                    @foreach($metals['items'][$symbol]['prices'] as $currency => $price)
                                        <div class="metal-price">
                                            <div class="metal-price-code">{{ $currency }}</div>
                                            <div class="metal-price-value">
                                                {{ number_format((float) $price, 2) }} {{ $currencySymbols[$currency] ?? $currency }}
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </article>
                        @endif
                    @endforeach
                </div>

                @if($metals['gold_karats'] !== [])
                    <div class="market-table-wrap">
                        <table class="market-table">
                            <thead>
                                <tr>
                                    <th>{{ __('markets.gold_karat') }}</th>
                                    <th>{{ __('markets.purity') }}</th>
                                    @foreach($metals['display_currencies'] as $currency)
                                        <th>{{ __('markets.price_per_gram') }} ({{ $currency }})</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($metals['gold_karats'] as $goldKarat)
                                    <tr>
                                        <td>
                                            <span class="karat-label">
                                                <span class="karat-dot"></span>
                                                {{ __('markets.karat_value', ['karat' => $goldKarat['karat']]) }}
                                            </span>
                                        </td>
                                        <td>{{ number_format($goldKarat['purity'] * 100, 1) }}%</td>
                                        @foreach($metals['display_currencies'] as $currency)
                                            <td class="price-cell">
                                                @if(isset($goldKarat['prices'][$currency]))
                                                    {{ number_format((float) $goldKarat['prices'][$currency], 2) }}
                                                    {{ $currencySymbols[$currency] ?? $currency }}
                                                @else
                                                    —
                                                @endif
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            @else
                <div class="markets-alert">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                    <span>{{ __('markets.metals_unavailable') }}</span>
                </div>
            @endif
        </section>

        <section class="markets-section">
            <div class="markets-section-heading">
                <h2 class="markets-section-title">
                    <i class="fa-solid fa-chart-line"></i>
                    {{ __('markets.stock_markets') }}
                </h2>
                <span class="markets-section-note">{{ __('markets.stock_markets_note') }}</span>
            </div>

            <div class="tradingview-shell">
                <div class="tradingview-widget-container" style="height:100%;width:100%">
                    <div class="tradingview-widget-container__widget" style="height:calc(100% - 32px);width:100%"></div>
                    <div class="tradingview-widget-copyright">
                        <a href="https://www.tradingview.com/" rel="noopener nofollow" target="_blank">
                            <span class="blue-text">{{ __('markets.market_data_by_tradingview') }}</span>
                        </a>
                    </div>
                    <script type="text/javascript" src="https://s3.tradingview.com/external-embedding/embed-widget-market-overview.js" async>
                    {
                        "colorTheme": "dark",
                        "dateRange": "12M",
                        "showChart": true,
                        "locale": "{{ $tvLocale }}",
                        "width": "100%",
                        "height": "600",
                        "largeChartUrl": "",
                        "isTransparent": true,
                        "showSymbolLogo": true,
                        "showFloatingTooltip": false,
                        "tabs": [
                            {
                                "title": @json(__('markets.global_indices')),
                                "symbols": [
                                    {"s": "FOREXCOM:SPXUSD", "d": @json(__('markets.indices.sp500'))},
                                    {"s": "NASDAQ:NDX", "d": @json(__('markets.indices.nasdaq'))},
                                    {"s": "DJ:DJI", "d": @json(__('markets.indices.dow'))},
                                    {"s": "FTSE:UKX", "d": @json(__('markets.indices.ftse'))},
                                    {"s": "XETR:DAX", "d": @json(__('markets.indices.dax'))},
                                    {"s": "TVC:NI225", "d": @json(__('markets.indices.nikkei'))},
                                    {"s": "TADAWUL:TASI", "d": @json(__('markets.indices.tasi'))}
                                ],
                                "originalTitle": "Indices"
                            },
                            {
                                "title": @json(__('markets.commodities')),
                                "symbols": [
                                    {"s": "TVC:GOLD", "d": @json(__('markets.gold'))},
                                    {"s": "TVC:SILVER", "d": @json(__('markets.silver'))},
                                    {"s": "TVC:USOIL", "d": @json(__('markets.indices.crude_oil'))},
                                    {"s": "TVC:UKOIL", "d": @json(__('markets.indices.brent'))}
                                ],
                                "originalTitle": "Commodities"
                            }
                        ]
                    }
                    </script>
                </div>
            </div>

            <div class="market-disclaimer">
                <i class="fa-solid fa-shield-halved"></i>
                <span>{{ __('markets.disclaimer') }}</span>
            </div>
        </section>
    </div>
</div>
@endsection

@push('scripts')
@if($fx['available'])
<script>
document.addEventListener('DOMContentLoaded', function () {
    const rates = {{ Illuminate\Support\Js::from($fx['rates']) }};
    const locale = {{ Illuminate\Support\Js::from($locale) }};
    const amountInput = document.getElementById('marketAmount');
    const fromSelect = document.getElementById('marketFrom');
    const toSelect = document.getElementById('marketTo');
    const resultElement = document.getElementById('marketConversionResult');

    if (!amountInput || !fromSelect || !toSelect || !resultElement) {
        return;
    }

    const convert = function () {
        const amount = Number.parseFloat(amountInput.value);
        const from = fromSelect.value;
        const to = toSelect.value;

        if (!Number.isFinite(amount) || amount < 0 || !rates[from] || !rates[to]) {
            resultElement.textContent = '—';
            return;
        }

        const converted = (amount / Number(rates[from])) * Number(rates[to]);
        const formatted = new Intl.NumberFormat(locale, {
            minimumFractionDigits: 2,
            maximumFractionDigits: converted >= 100 ? 2 : 4,
        }).format(converted);

        resultElement.textContent = `${formatted} ${to}`;
    };

    amountInput.addEventListener('input', convert);
    fromSelect.addEventListener('change', convert);
    toSelect.addEventListener('change', convert);
    convert();
});
</script>
@endif
@endpush