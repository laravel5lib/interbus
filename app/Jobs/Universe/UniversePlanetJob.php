<?php

namespace App\Jobs\Universe;

use App\Jobs\PublicESIJob;
use App\Models\Universe\UniversePlanet;

class UniversePlanetJob extends PublicESIJob
{
    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $planet = $this->getClient()->invoke('/universe/planets/' . $this->getId())->get('result');

        foreach ($planet->pull('position') as $key => $pos) {
            $planet->put($key, $pos);
        }

        UniversePlanet::updateOrCreate(['planet_id' => $this->getId()],
            $planet->toArray()
        );
    }
}
