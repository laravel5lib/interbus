<?php

namespace App\Jobs\Character;

use App\Jobs\AuthenticatedESIJob;
use App\Models\Character\CharacterAsset;
use App\Models\Character\CharacterAssetName;
use App\Models\Universe\UniverseGroup;
use Carbon\Carbon;

class CharacterAssetNameJob extends AuthenticatedESIJob
{

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $nameableCategories = [6];
        $nameableGroups = [448];

        $typeIds = [];
        $groups = UniverseGroup::whereIn('category_id', $nameableCategories)
            ->orWhereIn('group_id', $nameableGroups)
            ->with('types')->get();

        foreach ($groups as $group) {
            foreach ($group['types'] as $type) {
                $typeIds[] = $type['type_id'];
            }
        };

        $assets = CharacterAsset::whereIn('type_id', $typeIds)->where('character_id', $this->getId())->with('item')->get();
        $fetchItems = $assets->pluck('item_id');
        //ESI only lets us fetch 1k at a time...
        foreach ($fetchItems->chunk(1000) as $items) {

            $names = $this->getClient()->invoke('/characters/' . $this->getId() . '/assets/names/',
                [
                    'json' => $items
                ], 'POST');

            $names = $names->get('result')->filter(function ($name){
                return $name['name'] !== 'None';
            })->keyBy('item_id');

            $existingNames = CharacterAssetName::select('item_id', 'name')->whereIn('item_id', $names->pluck('item_id'))->get();
            foreach ($existingNames as $existingName) {
                $esiName = $names[$existingName['item_id']];
                if ( $esiName != $existingName->toArray()) {
                    $existingName->fill($esiName);
                    $existingName->save();
                }
                $names->forget($existingName['item_id']);
            }

            if ($names->count()) {
                $names = $names->map(function ($name){
                    return array_merge($name, ['updated_at' => Carbon::now(), 'created_at' => Carbon::now()]);
                });
                CharacterAssetName::insert($names->toArray());
            }
        }
    }
}
