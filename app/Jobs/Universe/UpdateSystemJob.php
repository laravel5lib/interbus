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
        $planets = collect($system->pull('planets'));

        foreach ($system->pull('position') as $key => $pos) {
            $system->put($key, $pos);
        }

        foreach ($planets as $planet) {
            $planetId = $planet['planet_id'];
            dispatch(new UniversePlanetJob($planetId));

            if (isset($planet['moons'])) {
                foreach ($planet['moons'] as $moon) {
                    dispatch(new UniverseMoonJob($moon, $planetId));
                }
            }

            if (isset($planet['asteroid_belts'])) {
                foreach ($planet['asteroid_belts'] as $asteroidBelt) {
                    dispatch(new UniverseAsteroidBeltJob($asteroidBelt, $planetId));
                }
            }
        }

        foreach ($gates as $gate) {
            dispatch(new UniverseGateJob($gate));
        }

        foreach ($stations as $station) {
            dispatch(new UniverseStationJob($station));
        }

        UniverseSystem::updateOrCreate(['system_id' => $this->getId()],
            $system->toArray()
        );

        $this->logFinished();
    }
}
