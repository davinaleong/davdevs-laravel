<?php

use App\Http\Controllers\Auth\TwoFactorController;
use App\Http\Controllers\Panel\DashboardController;
use App\Http\Controllers\Panel\EntryController;
use App\Http\Controllers\Panel\ImageController;
use App\Http\Controllers\Panel\LinkController;
use App\Http\Controllers\Panel\VideoEmbedController;
use App\Http\Controllers\Panel\LayoutController;
use App\Http\Controllers\Panel\ContentTypeController;
use App\Http\Controllers\Panel\CategoryController;
use App\Http\Controllers\Panel\TagController;
use App\Http\Controllers\Panel\PublicationController;
use App\Http\Controllers\Panel\QuipController;
use App\Http\Controllers\Panel\SettingController;
use App\Http\Controllers\Panel\ActivityLogController;
use App\Http\Controllers\Site\SiteController;
use Illuminate\Support\Facades\Route;

// Public site — fixed paths first
Route::get('/', [SiteController::class, 'home'])->name('home');
Route::get('funny', [SiteController::class, 'funny'])->name('site.funny');
Route::get('api/quips/random', [SiteController::class, 'randomQuip'])->name('api.quips.random');
Route::get('api/search', [SiteController::class, 'search'])->middleware('throttle:30,1')->name('api.search');
Route::post('api/reactions/{type}/{id}', [\App\Http\Controllers\Api\ReactionController::class, 'toggle'])
    ->where('type', 'entry|publication')
    ->where('id', '[0-9]+')
    ->middleware('throttle:5,1')
    ->name('api.reactions.toggle');
Route::get('ebooks', [SiteController::class, 'ebooks'])->name('site.ebooks');
Route::get('ebooks/{slug}', [SiteController::class, 'ebookShow'])->name('site.ebooks.show');

// Auth
require __DIR__.'/auth.php';

// 2FA (after password auth, before CMS)
Route::middleware('auth')->prefix('2fa')->name('2fa.')->group(function () {
    Route::get('setup',     [TwoFactorController::class, 'setup'])->name('setup');
    Route::post('setup',    [TwoFactorController::class, 'storeSetup'])->name('setup.store');
    Route::get('challenge', [TwoFactorController::class, 'challenge'])->name('challenge');
    Route::post('challenge',[TwoFactorController::class, 'verify'])->name('verify');
    Route::get('save-codes',[TwoFactorController::class, 'saveCodes'])->name('save-codes');
    Route::get('recovery',  [TwoFactorController::class, 'recovery'])->name('recovery');
    Route::post('recovery', [TwoFactorController::class, 'verifyRecovery'])->name('recovery.verify');
    Route::post('disable',  [TwoFactorController::class, 'disable'])->name('disable');
});

// CMS panel
Route::middleware(['auth', 'two_factor'])->prefix('panel')->name('panel.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Content
    Route::resource('entries',      EntryController::class)->except(['show']);
    Route::post('entries/{entry}/duplicate', [EntryController::class, 'duplicate'])->name('entries.duplicate');
    Route::post('entries/bulk',    [EntryController::class, 'bulk'])->name('entries.bulk');

    Route::resource('publications', PublicationController::class)->except(['show']);

    // Media
    Route::resource('images',      ImageController::class)->except(['show']);
    Route::resource('links',       LinkController::class)->except(['show']);
    Route::post('links/reorder',  [LinkController::class, 'reorder'])->name('links.reorder');
    Route::resource('video-embeds',VideoEmbedController::class)->except(['show']);

    // Taxonomy
    Route::resource('layouts',      LayoutController::class)->except(['show']);
    Route::resource('content-types',ContentTypeController::class)->except(['show']);
    Route::resource('categories',   CategoryController::class)->except(['show']);
    Route::resource('tags',         TagController::class)->except(['show']);

    // Jokes
    Route::resource('quips', QuipController::class)->except(['show']);
    Route::post('quips/bulk-toggle', [QuipController::class, 'bulkToggle'])->name('quips.bulk-toggle');

    // Settings & logs
    Route::get('settings',        [SettingController::class, 'index'])->name('settings.index');
    Route::put('settings',        [SettingController::class, 'update'])->name('settings.update');
    Route::post('settings/flush-cache', [SettingController::class, 'flushCache'])->name('settings.flush-cache');
    Route::get('logs',            [ActivityLogController::class, 'index'])->name('logs.index');
});

// Static mockups (dev only)
if (app()->isLocal()) {
    Route::get('static/{slug}', function (string $slug) {
        abort_unless(preg_match('/^[a-z0-9]+(?:[\-][a-z0-9]+)*$/', $slug), 404);
        return view("static.{$slug}");
    })->where('slug', '[a-z0-9\-]+');
}

// Public site — dynamic content-type listing/detail routes (catch-all, MUST be last)
Route::get('{typeSlug}', [SiteController::class, 'listing'])
    ->where('typeSlug', '[a-z0-9\-]+')
    ->name('site.listing');
Route::get('{typeSlug}/{slug}', [SiteController::class, 'show'])
    ->where('typeSlug', '[a-z0-9\-]+')
    ->where('slug', '[a-z0-9\-]+')
    ->name('site.show');
