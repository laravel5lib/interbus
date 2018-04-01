<?php

namespace App\Jobs\Character;

use App\Jobs\AuthenticatedESIJob;
use App\Models\Character\CharacterSkill;
use tristanpollard\ESIClient\Services\ESIClient;

class CharacterSkillsJob extends AuthenticatedESIJob
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
        $response = $client->invoke("/characters/{$this->token->character_id}/skills");
        $skills = $response->get('result');

        //TODO  total/unallocated SP

        $totalSp = $skills->get('total_sp');
        $unallocatedSp = $skills->get('unallocated_sp');
        $skills = $skills->get('skills');

        foreach ($skills as $skill){
            CharacterSkill::updateOrCreate([
                'character_id' => $this->token->character_id, 'skill_id' => $skill['skill_id']
            ], $skill
            )->touch();
        }

        $this->logFinished();

    }
}
