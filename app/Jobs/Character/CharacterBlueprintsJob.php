<?php

namespace App\Jobs\Character;

use App\Models\Character\CharacterBlueprints;
use App\Jobs\AuthenticatedESIJob;
use Carbon\Carbon;
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
        $blueprints = $response->get('result')->keyBy('item_id');

        $itemIds = $blueprints->pluck('item_id');

        $fields = ['item_id', 'type_id', 'location_id', 'location_flag', 'quantity', 'time_efficiency', 'material_efficiency', 'runs'];
        $knownBp = CharacterBlueprints::select($fields)->whereIn('item_id', $itemIds)->where('character_id', $this->getId())->get()->keyBy('item_id');

        foreach ($knownBp as $blueprint) {
            $esiBp = $blueprints->get($blueprint['item_id']);
            if ($esiBp != $blueprint->toArray()) {
                $blueprint->fill($esiBp);
                $blueprint->save();
            }
            $blueprints->forget($blueprint['item_id']);
        }

        if ($blueprints->count()) {
            $time = Carbon::now();
            $blueprints = $blueprints->map(function ($item) use ($time){
                $item['character_id'] = $this->getId();
                $item['updated_at'] = $time;
                $item['created_at'] = $time;
                return $item;
            });
            CharacterBlueprints::insert($blueprints->toArray());
        }

        CharacterBlueprints::whereNotIn('item_id', $itemIds)->where('character_id', $this->getId())->delete();

        $this->logFinished();
    }
}
