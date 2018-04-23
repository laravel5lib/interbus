<?php

namespace App\Jobs\Universe;

use App\Jobs\PublicESIJob;
use App\Models\Universe\UniverseGate;
use App\Models\Universe\UniverseGateDestination;
use Illuminate\Support\Facades\DB;

class UniverseGateJob extends PublicESIJob
{

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $gate = $this->getClient()->invoke('/universe/stargates/' . $this->getId())->get('result');

        foreach ($gate->pull('position') as $key => $pos) {
            $gate->put($key, $pos);
        }

        DB::transaction(function ($db) use ($gate) {

            foreach ($gate->pull('destination') as $key => $value) {
                $gate->put('destination_' . $key, $value);
            }

            UniverseGate::updateOrCreate(['stargate_id' => $this->getId()], $gate->toArray());
        });

    }
}
