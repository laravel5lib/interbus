<?php

namespace App\Providers;

use App\Models\Alliance\Alliance;
use App\Models\Character\Character;
use App\Models\Corporation\Corporation;
use App\Models\Observers\TokenObserver;
use App\Models\Token;
use App\Models\Universe\UniverseStation;
use App\Models\Universe\UniverseStructure;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Token::observe(TokenObserver::class);

        Relation::morphMap([
            'character' => Character::class,
            'corporation' => Corporation::class,
            'alliance' => Alliance::class,
            'station' => UniverseStation::class,
            'structure' => UniverseStructure::class,
        ]);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
