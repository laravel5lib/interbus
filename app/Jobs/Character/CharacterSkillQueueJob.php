<?php

namespace App\Jobs\Character;

use App\Models\Character\CharacterSkill;
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
        $queue = $response->get('result')->keyBy(function ($item){
            return $item['skill_id'] . ':' . $item['finished_level'];
        });
        $queue = $queue->map(function ($queue){
            $queue['start_date'] = Carbon::parse($queue['start_date']);
            $queue['finish_date'] = Carbon::parse($queue['finish_date']);
            return $queue;
        });
        $ignore = $queue->pluck('skill_id', 'finished_level');

        $knownSkillQueues = CharacterSkillQueue::select('id', 'skill_id', 'finished_level', 'queue_position', 'start_date', 'finish_date', 'level_end_sp', 'level_start_sp', 'training_start_sp')
            ->where('character_id', $this->getId())
            ->whereIn('skill_id', $queue->pluck('skill_id'))
            ->get();

        foreach ($knownSkillQueues as $knownSkillQueue) {
            $key = $knownSkillQueue['skill_id'] . ':' . $knownSkillQueue['finished_level'];
            $esiSkillQueue = $queue[$key];

            $knownSkillQueue['start_date'] = Carbon::parse($knownSkillQueue['start_date']);
            $knownSkillQueue['finish_date'] = Carbon::parse($knownSkillQueue['finish_date']);

            if ($esiSkillQueue != collect($knownSkillQueue)->except(['id'])->toArray()) {
                $knownSkillQueue->fill($esiSkillQueue);
                $knownSkillQueue->save();
            }
            $queue->forget($key);
        }

        if ($queue->count()) {
            $time = Carbon::now();
            $queue = $queue->map(function ($queue) use ($time){
               return array_merge($queue, ['character_id' => $this->getId(), 'created_at' => $time, 'updated_at' => $time]);
            });
            CharacterSkillQueue::insert($queue->toArray());
        }

        $deleteQuery = CharacterSkillQueue::where('character_id', $this->getId());
        foreach ($ignore as $id => $finish ) {
            $deleteQuery->where(function ($query) use ($id, $finish) {
                $query->where('skill_id', '!=', $id)->where('finished_level', '!=', $finish);
            });
        }
        $deleteQuery->delete();

        $this->logFinished();
    }
}
