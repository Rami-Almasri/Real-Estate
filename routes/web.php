<?php

use App\Http\Controllers\Web\ContractWebController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\HomeController;
use App\Http\Controllers\Web\ListingWebController;
use App\Http\Controllers\Web\MarketController;
use App\Http\Controllers\Web\MatchWebController;
use App\Http\Controllers\Web\SubscriptionWebController;
use App\Http\Controllers\Web\WebAuthController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public storefront
|--------------------------------------------------------------------------
*/
Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/listings', [ListingWebController::class, 'index'])->name('listings.index');
Route::get('/listings/{house}', [ListingWebController::class, 'show'])->name('listings.show');

Route::get('/market', [MarketController::class, 'index'])->name('market.index');

Route::get('/pricing', [SubscriptionWebController::class, 'pricing'])->name('pricing');

// Buyer matching wizard (instant, no persistence) + live preview JSON.
Route::get('/match', [MatchWebController::class, 'wizard'])->name('match.wizard');
Route::get('/match/preview', [MatchWebController::class, 'preview'])->name('match.preview');

/*
|--------------------------------------------------------------------------
| Authentication (session guard)
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [WebAuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [WebAuthController::class, 'login']);
    Route::get('/register', [WebAuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [WebAuthController::class, 'register']);
});
Route::post('/logout', [WebAuthController::class, 'logout'])->middleware('auth')->name('logout');

/*
|--------------------------------------------------------------------------
| Buyer area
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/account/matches', [MatchWebController::class, 'myMatches'])->name('account.matches');
    Route::post('/account/preferences', [MatchWebController::class, 'storePreference'])->name('preferences.store');
    Route::delete('/account/preferences/{preference}', [MatchWebController::class, 'destroyPreference'])->name('preferences.destroy');
    Route::post('/account/matches/read', [MatchWebController::class, 'markAllRead'])->name('matches.read');
});

/*
|--------------------------------------------------------------------------
| Office dashboard (SaaS subscribers)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->prefix('dashboard')->name('dashboard.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('index');

    // Listings
    Route::get('/listings', [DashboardController::class, 'listings'])->name('listings');
    Route::get('/listings/create', [DashboardController::class, 'createListing'])->name('listings.create');
    Route::post('/listings', [DashboardController::class, 'storeListing'])->name('listings.store');
    Route::delete('/listings/{house}', [DashboardController::class, 'destroyListing'])->name('listings.destroy');

    // Buyer leads matched to this office's listings
    Route::get('/leads', [DashboardController::class, 'leads'])->name('leads');

    // Contracts
    Route::get('/contracts', [ContractWebController::class, 'index'])->name('contracts');
    Route::get('/contracts/create', [ContractWebController::class, 'create'])->name('contracts.create');
    Route::post('/contracts', [ContractWebController::class, 'store'])->name('contracts.store');
    Route::get('/contracts/{contract}/pdf', [ContractWebController::class, 'download'])->name('contracts.pdf');

    // Subscription
    Route::get('/subscription', [SubscriptionWebController::class, 'show'])->name('subscription');
    Route::post('/subscription/{plan}', [SubscriptionWebController::class, 'subscribe'])->name('subscription.subscribe');
});
