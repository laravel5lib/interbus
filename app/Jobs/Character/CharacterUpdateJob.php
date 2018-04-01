<?php

namespace App\Jobs\Character;

use App\Jobs\Alliance\AllianceUpdateJob;
use App\Jobs\Corporation\CorporationUpdateJob;
use App\Jobs\PublicESIJob;
use App\Models\Character\Character;
use Carbon\Carbon;

class CharacterUpdateJob extends PublicESIJob{



     /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->logStart();

        $client = $this->getClient();
        $response = $client->invoke("/characters/{$this->getId()}/");
        $response = $response->get('result');

        $response->put('birthday', Carbon::parse($response->get('birthday')));

        $character = Character::updateOrCreate(
            ['character_id' => $this->getId()],
            $response->toArray()
        );
        $character->touch();

        if (!$character->corporation()->first()){
            dispatch(new CorporationUpdateJob($character->corporation_id));
        }

        if ($character->alliance_id && !$character->alliance()->first()){
            dispatch(new AllianceUpdateJob($character->alliance_id));
        }

        $this->logFinished();
    }
}
