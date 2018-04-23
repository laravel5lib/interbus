<?php

namespace App\Jobs\Character;

use App\Jobs\AuthenticatedESIJob;
use App\Models\Character\CharacterSkill;
use Carbon\Carbon;
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

        $skills = collect($skills->get('skills'))->keyBy('skill_id');
        $knownSkills = CharacterSkill::select('id', 'skill_id', 'skillpoints_in_skill', 'trained_skill_level', 'active_skill_level')->where('character_id', $this->getId())->whereIn('skill_id', $skills->pluck('skill_id'))->get();

        foreach ($knownSkills as $knownSkill) {
            $esiSkill = $skills[$knownSkill['skill_id']];
            if ($esiSkill != collect($knownSkill)->except(['id'])->toArray()) {
                $knownSkill->fill($esiSkill);
                $knownSkill->save();
            }
            $skills->forget($knownSkill['skill_id']);
        }

        if ($skills->count()) {
            $time = Carbon::now();
            $skills = $skills->map(function ($skill) use ($time) {
                return  array_merge(['character_id' => $this->getId(), 'updated_at' => $time, 'created_at' => $time], $skill);
            });
            CharacterSkill::insert($skills->toArray());
        }

        $this->logFinished();

    }
}
