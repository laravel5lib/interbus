<?php

namespace App\Jobs\Character;

use App\Jobs\AuthenticatedESIJob;
use App\Models\Character\CharacterMining;

class CharacterMiningJob extends AuthenticatedESIJob
{

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->logStart();

        $pages = 1;
        for ($i = 1; $i <= $pages; $i++) {
            $mining = $this->getClient()->invoke('/characters/' . $this->getId() . '/mining', [
                'query' => [
                    'page' => $i
                ]
            ]);
            $pages = $mining->get('headers')['X-Pages'][0];
            foreach ($mining->get('result') as $entry) {
                CharacterMining::updateOrCreate( array_merge(['character_id' => $this->getId()], $entry),
                    []
                );
            }
        }

        $this->logFinished();
    }
}
