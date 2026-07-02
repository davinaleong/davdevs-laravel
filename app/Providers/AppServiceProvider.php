<?php

namespace App\Providers;

use App\Models\Entry;
use App\Models\Publication;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Relation::enforceMorphMap([
            'entry'       => Entry::class,
            'publication' => Publication::class,
        ]);
    }
}
