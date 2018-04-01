<?php

namespace App\Jobs\Character;

use App\Models\Character\CharacterFatigue;
use Carbon\Carbon;
use tristanpollard\ESIClient\Services\ESIClient;
use App\Jobs\AuthenticatedESIJob;

class CharacterFatigueJob extends AuthenticatedESIJob
{

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->logStart();
        $client = $this->getClient();

        $response = $client->invoke("/characters/{$this->token->character_id}/fatigue");
        $fatigue = $response->get('result');

        $fatigue = $fatigue->map(function ($item){
            return Carbon::parse($item);
        });

        CharacterFatigue::updateOrCreate(
            ['character_id' => $this->token->character_id],
            $fatigue->toArray()
        )->touch();

        $this->logFinished();
    }
}
