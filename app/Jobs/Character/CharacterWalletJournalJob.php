<?php

namespace App\Jobs\Character;

use App\Jobs\AuthenticatedESIJob;
use App\Jobs\Corporation\CorporationUpdateJob;
use App\Models\Character\Character;
use App\Models\Character\CharacterJournalEntry;
use App\Models\Corporation\Corporation;
use App\Models\Token;
use Carbon\Carbon;

class CharacterWalletJournalJob extends AuthenticatedESIJob
{

    protected $scope = 'esi-wallet.read_character_wallet.v1';

    protected $from;

    public function __construct(Token $token, int $from = null)
    {
        parent::__construct($token);
        $this->from = $from;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->logStart();

        $client = $this->getClient();
        $params = [];
        if ($this->from) {
            $params['from_id'] = $this->from;
        }
        $response = $client->invoke("/characters/{$this->getId()}/wallet/journal", $params);
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

        DB::transaction(function ($db) use ($journal) {
            foreach ($journal as $entry) {
                $entry['date'] = Carbon::parse($entry['date']);
                unset($entry['extra_info']);
                CharacterJournalEntry::updateOrCreate(['ref_id' => $entry['ref_id']],
                    $entry
                );
            }
        });

        $this->logFinished();
    }

}
