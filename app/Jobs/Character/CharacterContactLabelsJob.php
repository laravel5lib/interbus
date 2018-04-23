<?php

namespace App\Jobs\Character;

use App\Models\Character\CharacterContactLabel;
use App\Jobs\AuthenticatedESIJob;
use Carbon\Carbon;

class CharacterContactLabelsJob extends AuthenticatedESIJob
{

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $labels = $this->getClient()->invoke('/characters/' . $this->getId() . '/contacts/labels')->get('result');
        $keyedLabels = $labels->keyBy('label_id');

        $existingLabels = CharacterContactLabel::select('label_id', 'label_name')
            ->where('character_id', $this->getId())
            ->whereIn('label_id', $keyedLabels->pluck('label_id'))
            ->get();

        //Probably better to just delete all and mass insert...
        foreach ($existingLabels as $existingLabel) {
            $esiLabel = $keyedLabels[$existingLabel['label_id']];
            if ( $esiLabel != $existingLabel->toArray() ) {
                $existingLabel->fill($esiLabel);
                $existingLabel->save();
            }
            $keyedLabels->forget($existingLabel['label_id']);
        }

        if ($keyedLabels->count()) {
            $keyedLabels = $keyedLabels->map(function ($label){
                return array_merge($label, ['character_id' => $this->getId(), 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
            });
            CharacterContactLabel::insert($keyedLabels->toArray());
        }

        CharacterContactLabel::where('character_id', $this->getId())->whereNotIn('label_id', $labels->pluck('label_id'))->delete();
    }
}
