<?php

namespace App\Http\Controllers;

use App\Services\MarketDataService;
use Illuminate\Contracts\View\View;

class MarketController extends Controller
{
    public function index(MarketDataService $marketDataService): View
    {
        $marketData = $marketDataService->getMarketData();

        return view('markets.index', compact('marketData'));
    }
}