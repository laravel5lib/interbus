<?php

namespace App\Jobs\Character;

use App\Models\Character\CharacterSkillQueue;
use Carbon\Carbon;
use tristanpollard\ESIClient\Services\ESIClient;
use App\Jobs\AuthenticatedESIJob;

class CharacterSkillQueueJob extends AuthenticatedESIJob
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
        $response = $client->invoke("/characters/{$this->token->character_id}/skillqueue");
        $queue = $response->get('result');

        //TODO remove old skills

        foreach ($queue as $skill){

            if (!empty($skill['finish_date'])){
                $skill['finish_date'] = Carbon::parse($skill['finish_date']);
            }

            if (!empty($skill['start_date'])){
                $skill['start_date'] = Carbon::parse($skill['start_date']);
            }

            CharacterSkillQueue::updateOrCreate([
                'character_id' => $this->token->character_id, 'skill_id' => $skill['skill_id'], 'finished_level' => $skill['finished_level']
            ], $skill
            )->touch();
        }

        $this->logFinished();
    }
}
