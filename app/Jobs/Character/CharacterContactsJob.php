<?php

namespace App\Jobs\Character;

use App\Jobs\Alliance\AllianceUpdateJob;
use App\Jobs\Corporation\CorporationUpdateJob;
use App\Models\Alliance\Alliance;
use App\Models\Character\Character;
use App\Models\Character\CharacterContact;
use App\Models\Corporation\Corporation;
use App\Jobs\AuthenticatedESIJob;

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
        $contacts = collect($response['result']);

        $charIds = $contacts->where('contact_type', 'character')->pluck('contact_id');
        $unknownChars = Character::whereIn('character_id', $charIds)->get();
        $unknownChars = $charIds->diff($unknownChars->pluck('character_id'));

        $corpIds = $contacts->where('contact_type', 'corporation')->pluck('contact_id');
        $unknownCorps = Corporation::whereIn('corporation_id', $corpIds)->get();
        $unknownCorps = $corpIds->diff($unknownCorps->pluck('corporation_id'));

        $allianceIds = $contacts->where('contact_type', 'alliance')->pluck('contact_id');
        $unknownAlliances = Alliance::whereIn('alliance_id', $allianceIds)->get();
        $unknownAlliances = $allianceIds->diff($unknownAlliances->pluck('alliance_id'));

        foreach ($contacts as $contact){
            CharacterContact::updateOrCreate([
                'owner_id' => $this->getId(),
                'contact_id' => $contact['contact_id']
            ], $contact
            )->touch();
        }

        CharacterContact::where('owner_id', $this->getId())->whereNotIn('contact_id', $contacts->pluck('contact_id'))->delete();

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
