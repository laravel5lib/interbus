<?php

namespace App\Jobs\Character;

use App\Models\Character\CharacterTitles;
use tristanpollard\ESIClient\Services\ESIClient;
use App\Jobs\AuthenticatedESIJob;

class CharacterTitlesJob extends AuthenticatedESIJob
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
        $response = $client->invoke("/characters/{$this->token->character_id}/titles");
        $titles = $response->get('result');

        DB::transaction(function ($db) use ($titles) {
            $titleIds = $titles->pluck('title_id');
            CharacterTitles::whereNotIn('title_id', $titleIds)->where('character_id', $this->getId())->delete();
            foreach ($titles as $title) {
                CharacterTitles::updateOrCreate(
                    ['character_id' => $this->token->character_id, 'title_id' => $title['title_id']],
                    $title
                );
            }
        });

        $this->logFinished();
    }
}
