<?php

namespace App\Jobs\Alliance;

use App\Jobs\Corporation\CorporationUpdateJob;
use App\Models\Corporation\Corporation;
use tristanpollard\ESIClient\Services\ESIClient;
use App\Jobs\PublicESIJob;

class AllianceCorporationsJob extends PublicESIJob
{

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(ESIClient $client)
    {
        $this->logStart();

        $response = $client->invoke("/alliances/{$this->id}/corporations/");
        $corps = $response->get('result');
        $unknownCorps = Corporation::whereIn('corporation_id', $corps)->get()->pluck('corporation_id');
        $unknownCorps = $corps->diff($unknownCorps);
        foreach ($unknownCorps as $corp){
            dispatch(new CorporationUpdateJob($corp));
        }

        $this->logFinished();
    }
}
