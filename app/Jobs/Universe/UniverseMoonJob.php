<?php

namespace App\Jobs\Universe;

use App\Jobs\PublicESIJob;
use App\Models\Universe\UniverseMoon;

class UniverseMoonJob extends PublicESIJob
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
        $moon = $this->getClient()->invoke('/universe/moons/' . $this->getId())->get('result');

        foreach ($moon->pull('position') as $key => $pos) {
            $moon->put($key, $pos);
        }

        UniverseMoon::updateOrCreate(['moon_id' => $this->getId()],
            $moon->toArray()
        );

    }
}
