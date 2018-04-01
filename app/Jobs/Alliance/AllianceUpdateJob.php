<?php

namespace App\Jobs\Alliance;

use App\Models\Alliance\Alliance;
use Carbon\Carbon;
use tristanpollard\ESIClient\Services\ESIClient;
use App\Jobs\PublicESIJob;

class AllianceUpdateJob extends PublicESIJob
{

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(ESIClient $client)
    {
        $this->logStart();

        $response = $client->invoke("/alliances/{$this->id}/");
        $result = $response->get('result');

        $result->put('date_founded', Carbon::parse($result->get('date_founded')));

        Alliance::updateOrCreate(['alliance_id' => $this->id],
            $result->toArray()
        );

        dispatch(new AllianceCorporationsJob($this->id));

        $this->logFinished();
    }
}
