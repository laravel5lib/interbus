<?php

namespace App\Jobs\Corporation;

use App\Models\Corporation\Corporation;
use App\Models\Corporation\CorporationHistory;
use Carbon\Carbon;
use tristanpollard\ESIClient\Services\ESIClient;
use App\Jobs\PublicESIJob;

class CorporationHistoryJob extends PublicESIJob{

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(ESIClient $client)
    {
        $this->logStart();

        $response = $client->invoke("/corporations/{$this->id}/alliancehistory/");
        $result = $response->get('result');

        $corp = Corporation::find($this->getId());
        $history = $corp->history()->get();
        $unknownHistory = $result->pluck('record_id')->diff($history->pluck('record_id'));
        $unknownHistory = $result->whereIn('record_id', $unknownHistory);

        foreach ($unknownHistory as $history){

           $history['start_date'] = Carbon::parse($history['start_date']);

           CorporationHistory::updateOrCreate([
               'corporation_id' => $this->getId(), 'record_id' => $history['record_id'],
               ], $history
           )->touch();
        }

        $this->logFinished();
    }
}
