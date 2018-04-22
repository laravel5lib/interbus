<?php

namespace App\Jobs\Universe;

use App\Jobs\PublicESIJob;
use App\Models\Universe\UniverseAsteroidBelt;

class UniverseAsteroidBeltJob extends PublicESIJob
{
    protected $planet;

    public function __construct(int $id, int $planet)
    {
        parent::__construct($id);
        $this->planet = $planet;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $asteroidBelt = $this->getClient()->invoke('/universe/asteroid_belts/' . $this->getId())->get('result');
        foreach ($asteroidBelt->pull('position') as $key => $pos) {
            $asteroidBelt->put($key, $pos);
        }
        $asteroidBelt->put('planet_id', $this->planet);

        UniverseAsteroidBelt::updateOrCreate(['asteroid_belt_id' => $this->getId()],
            $asteroidBelt->toArray()
        );
    }
}
