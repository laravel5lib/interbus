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

    protected $scope = 'esi-corporations.read_structures.v1';

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
        $structureServices = [];
        foreach ($knownStructures as $knownStructure) {
            $esiStructure = $structures->get($knownStructure['structure_id']);
            $services = $esiStructure['services'];
            $services = array_map(function ($service) use ($esiStructure) {
                $service['structure_id'] = $esiStructure['structure_id'];
                return $service;
            }, $services);
            $esiStructure['services'] = $services;
            if ($esiStructure == collect($knownStructure->toArray())->filter()->except(['created_at', 'updated_at'])->toArray()) {
                $services = $esiStructure['services'];
                $time = Carbon::now();
                unset($esiStructure['services']);
                $knownStructure->fill($esiStructure);
                $knownStructure->save();
                foreach ($services as $service) {
                    $deleteServiceQuery->where(function ($query) use ($service){
                        $query->where('state', '!=', $service['state']);
                        $query->where('name', '!=', $service['name']);
                        $query->where('structure_id', '!=', $service['structure_id']);
                    });
                }

            }
            $structures->forget($knownStructure['structure_id']);
        }

        if ($structures->count()) {
            DB::transaction(function ($db) use ($structures){
                $services = [];
                $time = Carbon::now();
                $columns = ['fuel_expires', 'next_reinforce_apply', 'next_reinforce_hour', 'next_reinforce_weekday', 'state_timer_end', 'state_timer_start', 'unanchors_at'];
                $dateFields = ['fuel_expires', 'next_reinforce_apply', 'state_time_end', 'state_timer_start', 'uanchors_at'];
                $structures = $structures->map(function ($value) use (&$services, $columns, $time, $dateFields){
                    if (isset($value['services'])) {
                        $services[$value['structure_id']] = array_merge(['structure_id' => $value['structure_id']], ...$value['services']);
                        unset($value['services']);
                    }
                    foreach ($dateFields as $dateField) {
                        if (isset($value[$dateField])) {
                            $value[$dateField] = Carbon::parse($value[$dateField]);
                        }
                    }
                    foreach ($columns as $column) {
                        if (!isset($value[$column])){
                            $value[$column] = null;
                        }
                    }
                    $value['updated_at'] = $time;
                    $value['created_at'] = $time;
                    return $value;
                });
                CorporationStructure::insert($structures->toArray());
                CorporationStructureService::insert($services);
            });
        }
    }
}
