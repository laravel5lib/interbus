<?php

namespace App\Jobs\Character;

use App\Models\Character\CharacterSkillQueue;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
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


        DB::transaction(function ($db) use ($queue) {
            $queueModel = CharacterSkillQueue::where('character_id', $this->getId());
            foreach ($queue as $skill) {
                $queueModel->where(function ($query) use ($skill) {
                    $query->where('skill_id', '!=', $skill['skill_id'])->where('finished_level', '!=',
                        $skill['finished_level']);
                });

                if (!empty($skill['finish_date'])) {
                    $skill['finish_date'] = Carbon::parse($skill['finish_date']);
                }

                if (!empty($skill['start_date'])) {
                    $skill['start_date'] = Carbon::parse($skill['start_date']);
                }

                CharacterSkillQueue::updateOrCreate([
                    'character_id' => $this->getId(),
                    'skill_id' => $skill['skill_id'],
                    'finished_level' => $skill['finished_level']
                ], $skill
                );
            }

            $queueModel->delete();
        });

        $this->logFinished();
    }
}
