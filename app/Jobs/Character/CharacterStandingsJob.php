<?php

namespace App\Jobs\Character;

use App\Jobs\AuthenticatedESIJob;
use App\Models\Character\CharacterStanding;
use Carbon\Carbon;

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
        $standings = $response->get('result')->keyBy('from_id');

        $knownStandings = CharacterStanding::select('id', 'from_id', 'from_type', 'standing')->where('character_id', $this->getId())
            ->whereIn('from_id', $standings->pluck('from_id'))
            ->get();
        foreach ($knownStandings as $knownStanding) {
            $esiStanding = $standings[$knownStanding['from_id']];
            if ($esiStanding != collect($knownStanding)->except(['id'])->toArray()){
                $knownStanding->fill($esiStanding);
                $knownStanding->save();
            }

            $standings->forget($knownStanding['from_id']);
        }

       if ($standings->count()) {
            $time = Carbon::now();
            $standings = $standings->map(function ($standing) use ($time) {
                return array_merge(['character_id' => $this->getId(), 'created_at' => $time, 'updated_at' => $time], $standing);
            });
            CharacterStanding::insert($standings->toArray());
       }

        $this->logFinished();
    }
}
