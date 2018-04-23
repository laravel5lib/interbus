<?php

namespace App\Jobs\Character;

use App\Jobs\AuthenticatedESIJob;
use App\Models\Character\CharacterAsset;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CharacterAssetsJob extends AuthenticatedESIJob
{
    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $pages = 1;
        $itemIds = collect([]);
        for ($i = 1; $i <= $pages; $i++) {
            $assets = $this->getClient()->invoke('/characters/' . $this->getId() . '/assets', [
                'query' => [
                    'page' => $i
                ]
            ]);
            $pages = $assets->get('headers')['X-Pages'][0];
            $assets = collect($assets->get('result'));

            $item_ids = $assets->pluck('item_id');
            // Retrieve the known assets, and then diff them. This allows us to do a mass insert of new assets, and ignore unchanged assets.
            // Saving on SQL queries.
            $knownAssets = CharacterAsset::select(['id', 'type_id', 'quantity', 'location_id', 'location_type', 'item_id', 'location_flag', 'is_singleton'])
                ->where('character_id', $this->getId())->whereIn('item_id', $item_ids)->get();

            $keyedAssets = $assets->keyBy('item_id');
            $knownAssets->map(function ($asset) use ($keyedAssets){
                $esiAsset = $keyedAssets->get($asset['item_id']);
                if ($esiAsset == collect($asset)->except(['id'])->toArray()) {
                    $keyedAssets->forget($asset['item_id']);
                    return;
                }
                //It's not the same, so lets update.
                $asset->fill($esiAsset);
                $asset->save();
            });

            $keyedAssets = $keyedAssets->map(function ($item){
                return array_merge($item, ['character_id' => $this->getId(), 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()] );
            });

            if ($keyedAssets->count()) {
                CharacterAsset::insert($keyedAssets->values()->toArray());
            }

            $itemIds = $itemIds->merge($assets->pluck('item_id'));
        }

        CharacterAsset::where('character_id', $this->getId())->whereNotIn('item_id', $itemIds)->delete();

        dispatch(new CharacterAssetNameJob($this->token));
    }
}
