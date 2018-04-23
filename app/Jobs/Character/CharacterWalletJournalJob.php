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

        $knownEntries = CharacterJournalEntry::select('ref_id')->whereIn('ref_id', $journal->pluck('ref_id'))
        ->get()->pluck('ref_id');
        $esiEntries = $journal->pluck('ref_id');
        $unknownEntries = $esiEntries->diff($knownEntries);

        $updateEntries = $journal->whereIn('ref_id', $unknownEntries);
        $time = Carbon::now();
        $keys = collect();
        $updateEntries = $updateEntries->map(function ($entry) use ($time, $keys){
            $item = array_merge($entry, ['created_at' => $time, 'updated_at' => $time, 'date' => Carbon::parse($entry['date'])]);
            unset($item['extra_info']);
            foreach (array_keys($item) as $key) {
                if (!$keys->contains($key)) {
                    $keys->push($key);
                }
            }
            foreach ($keys as $key) {
                if (!isset($item[$key])) {
                    $item[$key] = null;
                }
            }
            return $item;
        });

        if ($updateEntries->count()) {
            CharacterJournalEntry::insert($updateEntries->toArray());
        }

        $this->logFinished();
    }

}
