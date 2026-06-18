<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\AnalyticsService;

class MarketController extends Controller
{
    public function index(AnalyticsService $analytics)
    {
        return view('pages.market', $analytics->dashboard());
    }
}
