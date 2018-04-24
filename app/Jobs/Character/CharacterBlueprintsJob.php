<?php

namespace App\Jobs\Character;

use App\Models\Character\CharacterBlueprints;
use App\Jobs\AuthenticatedESIJob;
use Illuminate\Support\Facades\DB;

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

        $itemIds = $blueprints->pluck('item_id');
        CharacterBlueprints::whereNotIn('item_id', $itemIds)->where('character_id', $this->getId())->delete();

        //TODO make this more efficient (remove foreach and mass insert)
        foreach ($blueprints as $blueprint) {
            CharacterBlueprints::updateOrCreate(
                [
                    'character_id' => $this->getId(),
                    'item_id' => $blueprint['item_id']
                ],
                $blueprint
            );
        }

        $this->logFinished();
    }
}
