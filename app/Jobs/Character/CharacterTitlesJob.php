<?php

namespace App\Jobs\Character;

use App\Models\Character\CharacterTitles;
use Carbon\Carbon;
use tristanpollard\ESIClient\Services\ESIClient;
use App\Jobs\AuthenticatedESIJob;

class CharacterTitlesJob extends AuthenticatedESIJob
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
        $response = $client->invoke("/characters/{$this->token->character_id}/titles");
        $titles = $response->get('result')->keyBy('title_id');

        $knownTitles = CharacterTitles::select('id', 'title_id', 'name')->where('character_id', $this->getId())
            ->whereIn('title_id', $titles->pluck('title_id'))
            ->get();

        foreach ($knownTitles as $knownTitle) {
            $esiTitle = $titles[$knownTitle['title_id']];

            if ($esiTitle != collect($knownTitle)->except(['id'])->toArray()) {
                $knownTitle->fill($esiTitle);
               $knownTitle->save();
            }
            $titles->forget($knownTitle['title_id']);
        }

        if ($titles->count()) {
            $time = Carbon::now();
            $titles = $titles->map(function ($title) use ($time){
               return array_merge(['updated_at' => $time, 'created_at' => $time, 'character_id' => $this->getId()], $title);
            });
            CharacterTitles::insert($titles->toArray());
        }

        $this->logFinished();
    }
}
