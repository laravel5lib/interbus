<?php

namespace App\Jobs\Universe;

use App\Jobs\PublicESIJob;
use App\Models\Universe\UniverseGroup;
use tristanpollard\ESIClient\Services\ESIClient;

class UpdateGroupJob extends PublicESIJob
{

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(ESIClient $client)
    {
        $this->logStart();

        $group = $client->invoke('/universe/groups/' . $this->getId());
        $group = $group->get('result');
        $group->pull('types');

        UniverseGroup::updateOrCreate(['group_id' => $this->getId()],
            $group->toArray()
        );

        $this->logFinished();
    }
}
