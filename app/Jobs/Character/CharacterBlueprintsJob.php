<?php

namespace App\Jobs\Character;

use App\Models\Character\CharacterBlueprints;
use tristanpollard\ESIClient\Services\ESIClient;
use App\Jobs\AuthenticatedESIJob;

class CharacterBlueprintsJob extends AuthenticatedESIJob
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
        $response = $client->invoke("/characters/{$this->token->character_id}/blueprints");
        $blueprints = $response->get('result');

        //TODO remove all old BP's.

        foreach ($blueprints as $blueprint){
            CharacterBlueprints::updateOrCreate(
                [
                    'character_id' => $this->token->character_id,
                    'item_id' => $blueprint['item_id']
                ],
                $blueprint
            )->touch();
        }

        $this->logFinished();
    }
}
