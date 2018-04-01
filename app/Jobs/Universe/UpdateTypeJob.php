<?php

namespace App\Jobs\Universe;

use App\Jobs\PublicESIJob;
use App\Models\Universe\UniverseType;

class UpdateTypeJob extends PublicESIJob
{
    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->logStart();

        $type = $this->getClient()->invoke('/universe/types/' . $this->getId());
        $type = $type->get('result');

        $type->pull('dogma_attributes');
        $type->pull('dogma_effects');

        UniverseType::updateOrCreate(['type_id' => $this->getId()],
            $type->toArray()
        );

        $this->logFinished();
    }
}
