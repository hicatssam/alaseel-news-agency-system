<?php

namespace App\Services;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;
use Throwable;

class MarketDataService
{
    private const FX_CACHE_KEY = 'markets.fx.current.v2';

    private const FX_FALLBACK_KEY = 'markets.fx.last_success.v2';

    private const METALS_CACHE_KEY = 'markets.metals.current.v1';

    private const METALS_FALLBACK_KEY = 'markets.metals.last_success.v1';

    public function getMarketData(): array
    {
        $fx = $this->getExchangeRates();
        $metals = $this->getMetals();

        return [
            'fx' => $fx,
            'metals' => $this->prepareMetalPrices($metals, $fx),
            'has_any_data' => $fx['available'] || $metals['available'],
            'generated_at' => now()->toIso8601String(),
        ];
    }

    private function getExchangeRates(): array
    {
        return $this->rememberWithFallback(
            self::FX_CACHE_KEY,
            self::FX_FALLBACK_KEY,
            max(60, (int) config('markets.fx.cache_seconds', 21600)),
            fn (): array => $this->fetchExchangeRates(),
            [
                'available' => false,
                'stale' => false,
                'base' => strtoupper((string) config('markets.fx.base', 'USD')),
                'date' => null,
                'fetched_at' => null,
                'rates' => [],
            ],
            'exchange rates'
        );
    }

    private function fetchExchangeRates(): array
    {
        $base = strtoupper((string) config('markets.fx.base', 'USD'));
        $currencies = collect(config('markets.fx.currencies', []))
            ->map(fn ($currency) => strtoupper((string) $currency))
            ->filter(fn (string $currency) => preg_match('/^[A-Z]{3}$/', $currency) === 1)
            ->unique()
            ->values();

        if (! $currencies->contains($base)) {
            $currencies->prepend($base);
        }

        $quotes = $currencies
            ->reject(fn (string $currency) => $currency === $base)
            ->values()
            ->all();

        $response = Http::acceptJson()
            ->timeout(max(3, (int) config('markets.http_timeout', 10)))
            ->retry(2, 300)
            ->get((string) config('markets.fx.url'), [
                'base' => $base,
                'quotes' => implode(',', $quotes),
            ]);

        $this->ensureSuccessful($response, 'exchange rates');

        $rows = $response->json();

        if (! is_array($rows)) {
            throw new RuntimeException('The exchange-rate provider returned an invalid response.');
        }

        $rates = [$base => 1.0];
        $dates = [];

        foreach ($rows as $row) {
            if (! is_array($row)) {
                continue;
            }

            $quote = strtoupper((string) ($row['quote'] ?? ''));
            $rate = filter_var($row['rate'] ?? null, FILTER_VALIDATE_FLOAT);

            if (
                preg_match('/^[A-Z]{3}$/', $quote) !== 1
                || $rate === false
                || $rate <= 0
                || ! $currencies->contains($quote)
            ) {
                continue;
            }

            $rates[$quote] = (float) $rate;

            if (filled($row['date'] ?? null)) {
                $dates[] = (string) $row['date'];
            }
        }

        $orderedRates = [];

        foreach ($currencies as $currency) {
            if (array_key_exists($currency, $rates)) {
                $orderedRates[$currency] = $rates[$currency];
            }
        }

        if (count($orderedRates) < 2) {
            throw new RuntimeException('No usable exchange rates were returned.');
        }

        return [
            'available' => true,
            'stale' => false,
            'base' => $base,
            'date' => $dates === [] ? null : max($dates),
            'fetched_at' => now()->toIso8601String(),
            'rates' => $orderedRates,
        ];
    }

    private function getMetals(): array
    {
        return $this->rememberWithFallback(
            self::METALS_CACHE_KEY,
            self::METALS_FALLBACK_KEY,
            max(30, (int) config('markets.metals.cache_seconds', 300)),
            fn (): array => $this->fetchMetals(),
            [
                'available' => false,
                'stale' => false,
                'fetched_at' => null,
                'items' => [],
            ],
            'metal prices'
        );
    }

    private function fetchMetals(): array
    {
        $baseUrl = rtrim((string) config('markets.metals.url'), '/');
        $symbols = collect(config('markets.metals.symbols', ['XAU', 'XAG']))
            ->map(fn ($symbol) => strtoupper((string) $symbol))
            ->filter(fn (string $symbol) => in_array($symbol, ['XAU', 'XAG'], true))
            ->unique()
            ->values();

        $items = [];

        foreach ($symbols as $symbol) {
            try {
                $response = Http::acceptJson()
                    ->timeout(max(3, (int) config('markets.http_timeout', 10)))
                    ->retry(2, 300)
                    ->get("{$baseUrl}/price/{$symbol}/USD");

                $this->ensureSuccessful($response, "{$symbol} price");

                $payload = $response->json();

                if (! is_array($payload)) {
                    throw new RuntimeException("The provider returned an invalid {$symbol} price.");
                }

                $price = filter_var($payload['price'] ?? null, FILTER_VALIDATE_FLOAT);

                if ($price === false || $price <= 0) {
                    throw new RuntimeException("The provider returned an invalid {$symbol} price.");
                }

                $items[$symbol] = [
                    'symbol' => $symbol,
                    'name' => (string) ($payload['name'] ?? $symbol),
                    'currency' => 'USD',
                    'currency_symbol' => (string) ($payload['currencySymbol'] ?? '$'),
                    'price' => (float) $price,
                    'updated_at' => $this->normalizeDateTime($payload['updatedAt'] ?? null),
                ];
            } catch (Throwable $exception) {
                Log::warning('Market data provider failed for a metal symbol.', [
                    'symbol' => $symbol,
                    'message' => $exception->getMessage(),
                ]);
            }
        }

        if ($items === []) {
            throw new RuntimeException('No usable metal prices were returned.');
        }

        return [
            'available' => true,
            'stale' => false,
            'fetched_at' => now()->toIso8601String(),
            'items' => $items,
        ];
    }

    private function prepareMetalPrices(array $metals, array $fx): array
    {
        $metals['display_currencies'] = collect(config('markets.metals.display_currencies', ['USD']))
            ->map(fn ($currency) => strtoupper((string) $currency))
            ->filter(fn (string $currency) => preg_match('/^[A-Z]{3}$/', $currency) === 1)
            ->unique()
            ->values()
            ->all();
        $metals['gold_karats'] = [];

        if (! $metals['available']) {
            return $metals;
        }

        foreach ($metals['items'] as $symbol => $item) {
            $metals['items'][$symbol]['prices'] = $this->convertUsdPrice(
                (float) $item['price'],
                $metals['display_currencies'],
                $fx
            );
        }

        $goldOunceUsd = $metals['items']['XAU']['price'] ?? null;

        if (! is_numeric($goldOunceUsd)) {
            return $metals;
        }

        $gramsPerOunce = max(0.0001, (float) config('markets.troy_ounce_grams', 31.1034768));
        $karats = config('markets.gold_karats', []);

        foreach ($karats as $karat => $purity) {
            $purity = (float) $purity;

            if ($purity <= 0 || $purity > 1) {
                continue;
            }

            $gramUsd = ((float) $goldOunceUsd / $gramsPerOunce) * $purity;

            $metals['gold_karats'][] = [
                'karat' => (int) $karat,
                'purity' => $purity,
                'prices' => $this->convertUsdPrice(
                    $gramUsd,
                    $metals['display_currencies'],
                    $fx
                ),
            ];
        }

        return $metals;
    }

    private function convertUsdPrice(float $price, array $currencies, array $fx): array
    {
        $prices = [];
        $rates = $fx['rates'] ?? [];

        foreach ($currencies as $currency) {
            if ($currency === 'USD') {
                $prices[$currency] = $price;
                continue;
            }

            if ($fx['available'] && isset($rates[$currency]) && is_numeric($rates[$currency])) {
                $prices[$currency] = $price * (float) $rates[$currency];
            }
        }

        return $prices;
    }

    private function rememberWithFallback(
        string $cacheKey,
        string $fallbackKey,
        int $seconds,
        callable $resolver,
        array $emptyValue,
        string $context
    ): array {
        $cached = Cache::get($cacheKey);

        if (is_array($cached)) {
            $cached['stale'] = false;

            return $cached;
        }

        try {
            $fresh = $resolver();
            Cache::put($cacheKey, $fresh, now()->addSeconds($seconds));
            Cache::forever($fallbackKey, $fresh);

            return $fresh;
        } catch (Throwable $exception) {
            Log::warning("Unable to refresh {$context}.", [
                'message' => $exception->getMessage(),
            ]);

            $fallback = Cache::get($fallbackKey);

            if (is_array($fallback)) {
                $fallback['stale'] = true;

                return $fallback;
            }

            return $emptyValue;
        }
    }

    private function ensureSuccessful(Response $response, string $context): void
    {
        if ($response->successful()) {
            return;
        }

        throw new RuntimeException(
            "The {$context} provider returned HTTP {$response->status()}."
        );
    }

    private function normalizeDateTime(mixed $value): string
    {
        if (! is_string($value) || trim($value) === '') {
            return now()->toIso8601String();
        }

        try {
            return (new \DateTimeImmutable($value))->format(DATE_ATOM);
        } catch (Throwable) {
            return now()->toIso8601String();
        }
    }
}