<?php

namespace App\Jobs\Character;

use App\Jobs\Alliance\AllianceUpdateJob;
use App\Jobs\Corporation\CorporationUpdateJob;
use App\Models\Alliance\Alliance;
use App\Models\Character\Character;
use App\Models\Character\CharacterContact;
use App\Models\Corporation\Corporation;
use App\Jobs\AuthenticatedESIJob;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CharacterContactsJob extends AuthenticatedESIJob{

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        $this->logStart();

        $client = $this->getClient();

        $response = $client->invoke("/characters/{$this->getId()}/contacts");
        $contacts = collect($response['result'])->keyBy('contact_id');

        $charIds = $contacts->where('contact_type', 'character')->pluck('contact_id');
        $unknownChars = Character::whereIn('character_id', $charIds)->get();
        $unknownChars = $charIds->diff($unknownChars->pluck('character_id'));

        $corpIds = $contacts->where('contact_type', 'corporation')->pluck('contact_id');
        $unknownCorps = Corporation::whereIn('corporation_id', $corpIds)->get();
        $unknownCorps = $corpIds->diff($unknownCorps->pluck('corporation_id'));

        $allianceIds = $contacts->where('contact_type', 'alliance')->pluck('contact_id');
        $unknownAlliances = Alliance::whereIn('alliance_id', $allianceIds)->get();
        $unknownAlliances = $allianceIds->diff($unknownAlliances->pluck('alliance_id'));

        $allIds = $contacts->pluck('contact_id');
        CharacterContact::whereNotIn('contact_id', $allIds)->where('owner_id', $this->getId())->delete();

        $existingContacts = CharacterContact::select('id', 'contact_id', 'contact_type', 'standing', 'is_watched', 'label_id')->whereIn('contact_id', $allIds)->where('owner_id', $this->getId())->get();
        foreach ($existingContacts as $existingContact) {
            //Strip all null values for comparison...
            $existingContact = collect($existingContact)->filter(function ($value){
                return $value !== null;
            });
            $esiContact = $contacts[$existingContact['contact_id']];
            if ( $esiContact != collect($existingContact)->except(['id'])->toArray() ) {
                $existingContact->fill($esiContact);
                $existingContact->save();
            }
            $contacts->forget($existingContact['contact_id']);
        }

        if ($contacts->count()) {
            $contacts = $contacts->map(function ($contact){
               return array_merge([
                   'owner_id' => $this->getId(),
                   'updated_at' => Carbon::now(),
                   'created_at' => Carbon::now(),
                   'is_watched' => null,
                   'label_id' => null
               ], $contact);
            });
            CharacterContact::insert($contacts->toArray());
        }

        foreach ($unknownChars as $char){
            dispatch(new CharacterUpdateJob($char));
        }

        foreach ($unknownCorps as $corp){
            dispatch(new CorporationUpdateJob($corp));
        }

        foreach ($unknownAlliances as $alliance){
            dispatch(new AllianceUpdateJob($alliance));
        }

        $this->logFinished();
    }
}
