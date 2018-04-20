<?php

namespace App\Jobs\Character;

use App\Jobs\AuthenticatedESIJob;
use App\Jobs\Corporation\CorporationUpdateJob;
use App\Models\Character\Character;
use App\Models\Character\CharacterJournalEntry;
use App\Models\Corporation\Corporation;
use Carbon\Carbon;

class CharacterWalletJournalJob extends AuthenticatedESIJob
{

    protected $scope = 'esi-wallet.read_character_wallet.v1';


    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->logStart();

        $client = $this->getClient();
        $response = $client->invoke("/characters/{$this->getId()}/wallet/journal");
        $journal = collect($response->get('result'));

        $chars = $journal->where('first_party_type', 'character')->pluck('first_party_id');
        $chars = $chars->merge( $journal->where('second_party_type', 'character')->pluck('second_party_id') );

        $corps = $journal->where('first_party_type', 'corporation')->pluck('first_party_id');
        $corps = $corps->merge( $journal->where('second_party_type', 'corporation')->pluck('second_party_id') );

        $existingChars = Character::select('character_id')->whereIn('character_id', $chars)->get()->pluck('character_id');
        $existingCorps = Corporation::select('corporation_id')->whereIn('corporation_id', $corps)->get()->pluck('corporation_id');

        $unknownChars = $chars->diff($existingChars);
        $unknownCorps = $corps->diff($existingCorps);

        foreach ($unknownChars as $char) {
            dispatch(new CharacterUpdateJob($char));
        }
        foreach ($unknownCorps as $corp) {
            dispatch(new CorporationUpdateJob($corp));
        }

        foreach ($journal as $entry) {
            $entry['date'] = Carbon::parse($entry['date']);
            unset($entry['extra_info']);
            CharacterJournalEntry::updateOrCreate(['ref_id' => $entry['ref_id']],
                $entry
            );
        }

        $this->logFinished();
    }
}