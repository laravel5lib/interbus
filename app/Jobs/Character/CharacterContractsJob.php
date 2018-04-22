<?php

namespace App\Jobs\Character;

use App\Models\Character\CharacterContract;
use Carbon\Carbon;
use tristanpollard\ESIClient\Services\ESIClient;
use App\Jobs\AuthenticatedESIJob;

class CharacterContractsJob extends AuthenticatedESIJob
{
    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->logStart();

        //TODO queue character update jobs if unknown
        //TODO Pages
        //TODO contract items

        $client = $this->getClient();
        $response = $client->invoke("/characters/{$this->token->character_id}/contracts");
        $contracts = $response->get('result');

        DB::transaction(function ($db) use($contracts) {
            foreach ($contracts as $contract) {

                $contract['date_issued'] = Carbon::parse($contract['date_issued']);
                $contract['date_expired'] = Carbon::parse($contract['date_expired']);

                if (!empty($contract['date_accepted'])) {
                    $contract['date_accepted'] = Carbon::parse($contract['date_accepted']);
                }

                if (!empty($contract['date_completed'])) {
                    $contract['date_completed'] = Carbon::parse($contract['date_completed']);
                }

                CharacterContract::updateOrCreate([
                    'contract_id' => $contract['contract_id']
                ], $contract
                );
            }
        });

        $this->logFinished();
    }
}
