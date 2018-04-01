<?php

namespace App\Jobs\Character;

use App\Jobs\AuthenticatedESIJob;
use App\Models\Character\CharacterStanding;

class CharacterStandingsJob extends AuthenticatedESIJob
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
        $response = $client->invoke("/characters/{$this->getId()}/standings");
        $standings = $response->get('result');

        foreach ($standings as $standing){
            CharacterStanding::updateOrCreate([
                'character_id' => $this->getId(), 'from_id' => $standing['from_id']
            ], $standing
            )->touch();
        }

        $this->logFinished();
    }
}
