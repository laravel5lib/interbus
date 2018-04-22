<?php

namespace App\Jobs\Corporation;

use App\Models\Corporation\Corporation;
use Carbon\Carbon;
use tristanpollard\ESIClient\Services\ESIClient;
use App\Jobs\PublicESIJob;

class CorporationUpdateJob extends PublicESIJob{


    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(ESIClient $client)
    {
        $this->logStart();

        $response = $client->invoke("/corporations/{$this->id}/");
        $result = $response->get('result');

        if ($result->has('date_founded')){
            $result->put('date_founded', Carbon::parse($result->get('date_founded')));
        }

        Corporation::updateOrCreate(
            ['corporation_id' => $this->getId()],
            $result->toArray()
        );

        dispatch(new CorporationHistoryJob($this->id));

        $this->logFinished();
    }
}
