<?php

namespace App\Jobs\Universe;

use App\Jobs\PublicESIJob;
use App\Models\Universe\UniverseStation;
use App\Models\Universe\UniverseStationService;
use Illuminate\Support\Facades\DB;

class UniverseStationJob extends PublicESIJob
{

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $station = $this->getClient()->invoke('/universe/stations/' . $this->getId())->get('result');

        foreach ($station->pull('position') as $key => $pos) {
            $station->put($key, $pos);
        }
        DB::transaction(function ($db) use ($station){
            $services = $station->pull('services');
            $stationModel = UniverseStation::updateOrCreate(['station_id' => $this->getId()],
                $station->toArray()
            );

            UniverseStationService::where('station_id', $this->getId())->whereNotIn('service', $services)->delete();
            foreach ($services as $service) {
                $serviceData = ['station_id' => $this->getId(), 'service' => $service];
                UniverseStationService::updateOrCreate($serviceData, []);
            }
        });
    }
}
