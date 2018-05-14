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

    protected $optionalColumns = ['finish_date', 'level_end_sp', 'level_start_sp', 'start_date', 'training_start_sp'];

    protected $dateFields = ['start_date', 'finish_date'];

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
        $queue = $this->mapDateColumns($queue);
        $ignore = $queue->values();

        $knownSkillQueues = CharacterSkillQueue::select('id', 'skill_id', 'finished_level', 'queue_position', 'start_date', 'finish_date', 'level_end_sp', 'level_start_sp', 'training_start_sp')
            ->where('character_id', $this->getId())
            ->whereIn('skill_id', $queue->pluck('skill_id'))
            ->get();

        foreach ($knownSkillQueues as $knownSkillQueue) {
            $key = $knownSkillQueue['skill_id'] . ':' . $knownSkillQueue['finished_level'];
            if (!isset($queue[$key])) {
                $knownSkillQueue->delete();
                continue;
            }
            $esiSkillQueue = $queue[$key];

            $knownSkillQueue = $this->mapDateColumn($knownSkillQueue);
            if ($esiSkillQueue != collect($knownSkillQueue)->except(['id'])->filter()->toArray()) {
                $esiSkillQueue = $this->mapRequiredColumn($esiSkillQueue);
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
        foreach ($ignore as $ignoreQueue ) {
            $deleteQuery->where(function ($query) use ($ignoreQueue) {
                $query->where('skill_id', '!=', $ignoreQueue['skill_id']);
                $query->where('finished_level', '!=', $ignoreQueue['finished_level']);
            });
        }
        $deleteQuery->orWhereNotIn('skill_id', $ignore->pluck('skill_id'));
        $deleteQuery->delete();

        $this->logFinished();
    }
}
