<?php

namespace App\Jobs\Universe;

use App\Jobs\PublicESIJob;
use App\Models\Universe\UniverseSystem;
use tristanpollard\ESIClient\Services\ESIClient;

class UpdateSystemJob extends PublicESIJob
{

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(ESIClient $client)
    {
        $this->logStart();

        $system = $client->invoke('/universe/systems/' . $this->getId())->get('result');

        //TODO stargates
        //TODO stations
        //TODO planets

        $stations = $system->pull('stations');
        $gates = $system->pull('stargates');
        $planets = $system->pull('planets');

        foreach ($system->pull('position') as $key => $pos) {
            $system->put($key, $pos);
        }

        UniverseSystem::updateOrCreate(['system_id' => $this->getId()],
            $system->toArray()
        );

        $this->logFinished();
    }
}
