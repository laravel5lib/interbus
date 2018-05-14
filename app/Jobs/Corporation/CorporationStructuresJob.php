<?php

namespace App\Jobs\Corporation;

use App\CorporationStructure;
use App\Jobs\AuthenticatedESIJob;
use App\Jobs\CorporationJob;
use App\Models\Corporation\CorporationStructureService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CorporationStructuresJob extends AuthenticatedESIJob
{

    use CorporationJob;

    //protected $scope = 'esi-corporations.read_structures.v1';

    protected $role = 'Station_Manager';

    protected $optionalColumns = ['fuel_expires', 'next_reinforce_apply', 'next_reinforce_hour', 'next_reinforce_weekday', 'state_timer_end', 'state_timer_start', 'unanchors_at'];

    protected $dateFields = ['fuel_expires', 'next_reinforce_apply', 'state_timer_end', 'state_timer_start', 'unanchors_at'];

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        $structures = $this->getClient()->invoke('/corporations/' . $this->getId() . '/structures/');
        $structures = $structures->get('result')->keyBy('structure_id');

        $knownStructures = CorporationStructure::where('corporation_id', $this->getId())
            ->whereIn('structure_id', $structures->keys())
            ->with(['services' => function ($query) {
                $query->select('structure_id', 'state', 'name');
            }])
            ->get();

        $deleteServiceQuery = CorporationStructureService::query();
        $services = [];
        foreach ($knownStructures as $knownStructure) {
            $esiStructure = $structures->get($knownStructure['structure_id']);
            if (isset($esiStructure['services'])) {
                $services = $esiStructure['services'];
                $services = array_map(function ($service) use ($esiStructure) {
                    $service['structure_id'] = $esiStructure['structure_id'];
                    return $service;
                }, $services);
                $esiStructure['services'] = $services;
            }

            $esiStructure = $this->mapDateColumn($esiStructure);
            $compare = collect($knownStructure->toArray())->filter()->except(['created_at', 'updated_at']);
            $compare = $this->mapDateColumn($compare->toArray());
            if ($esiStructure != $compare) {
                $esiStructure = $this->mapRequiredColumn($esiStructure);
                $esiStructure = $this->mapDateColumn($esiStructure);

                if (isset($esiStructure['services'])) {
                    $services = $esiStructure['services'];
                    unset($esiStructure['services']);
                    foreach ($services as $service) {
                        $deleteServiceQuery->where(function ($query) use ($service){
                            $query->where('state', '!=', $service['state']);
                            $query->where('name', '!=', $service['name']);
                            $query->where('structure_id', '!=', $service['structure_id']);
                        });
                    }
                    $knownStructure->services()->delete();
                    $knownStructure->services()->insert($services);
                }

                $knownStructure->fill($esiStructure);
                $knownStructure->updated_at = Carbon::now();
                $knownStructure->save();
            }
            $structures->forget($knownStructure['structure_id']);
        }

        if ($structures->count()) {

            $services = [];
            $structures = $structures->map(function ($value) use (&$services){
                if (isset($value['services'])) {
                    foreach ($value['services'] as $service) {
                        $services[$value['structure_id']][] = array_merge(['structure_id' => $value['structure_id']], $service);
                    }
                    unset($value['services']);
                }
                return $value;
            });
            $services = collect($services)->flatten(1);
            $services = $this->addDateFields($services);
            $structures = $this->mapRequiredColumns($structures);
            $structures = $this->mapDateColumns($structures);
            $structures = $this->addDateFields($structures);
            DB::transaction(function ($db) use ($structures, $services){
                CorporationStructure::insert($structures->toArray());
                CorporationStructureService::insert($services->toArray());
            });
        }
    }
}
