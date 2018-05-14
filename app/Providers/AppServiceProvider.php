<?php

namespace App\Providers;

use App\Events\TokenInvalidated;
use App\Jobs\Character\CharacterRolesJob;
use App\Models\Alliance\Alliance;
use App\Models\Character\Character;
use App\Models\Character\CharacterRoles;
use App\Models\Corporation\Corporation;
use App\Models\Observers\TokenObserver;
use App\Models\Token;
use App\Models\Universe\UniverseStation;
use App\Models\Universe\UniverseStructure;
use App\Services\DiscordWebhookService;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Queue\Events\JobFailed;
use Illuminate\Queue\Events\JobProcessing;
use Illuminate\Queue\Jobs\SyncJob;
use Illuminate\Support\Facades\Queue;
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
            'other' => UniverseStructure::class,
        ]);

        Queue::before(function (JobProcessing $event){
            // TODO take into account DT
        });

        Queue::failing(function (JobFailed $event) {
            // TODO setup notifications.
            $command = unserialize($event->job->payload()['data']['command']);
            //Roles Changed

            $exception = $event->exception;
            if ($exception instanceof ClientException) {
                /** @var ClientException $exception */
                $body = json_decode($exception->getResponse()->getBody(), true);

                if ($exception->getCode() === 403 && mb_stripos($body['error'], 'role' !== false)) {
                    /** @var Character $character */
                    $character = Character::find($command->getCharacterId());
                    $token = $character->token()->first();
                    $character->roles()->delete();
                    if ($token) {
                        dispatch(new CharacterRolesJob($token));
                    }
                }else if ($exception->getCode() === 400 && $body['error'] === 'invalid_token') {
                    $token = Character::find($command->getCharacterId())->token()->first();
                    event(new TokenInvalidated($token));
                }
            }
        });
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
