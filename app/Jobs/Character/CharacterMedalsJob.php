<?php

namespace App\Jobs\Character;

use App\Jobs\AuthenticatedESIJob;
use App\Models\Character\CharacterMedal;
use Carbon\Carbon;

class CharacterMedalsJob extends AuthenticatedESIJob
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
        $response = $client->invoke("/characters/{$this->getId()}/medals");
        $medals = $response->get('result');

        $medalIds = $medals->pluck('medal_id');
        CharacterMedal::whereNotIn('medal_id', $medalIds)->where('character_id', $this->getId())->delete();

        foreach ($medals as $medal) {
            $medal['date'] = Carbon::parse($medal['date']);
            $medal = collect($medal);
            $graphics = $medal->pull('graphics');
            CharacterMedal::updateOrCreate([
                'character_id' => $this->getId(),
                'medal_id' => $medal['medal_id']
            ], $medal->toArray()
            );
        }

        $this->logFinished();
    }
}
