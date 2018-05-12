<?php

namespace App\Jobs\Character;

use App\Jobs\AuthenticatedESIJob;
use App\Jobs\Corporation\CorporationUpdateJob;
use App\Models\Character\Character;
use App\Models\Character\CharacterJournalEntry;
use App\Models\Corporation\Corporation;
use App\Models\Token;
use Carbon\Carbon;
use GuzzleHttp\RequestOptions;

class CharacterWalletJournalJob extends AuthenticatedESIJob
{

    protected $scope = 'esi-wallet.read_character_wallet.v1';

    protected $page;

    protected $optionalColumns = [
        'amount',
        'balance',
        'context_id',
        'context_id_type',
        'first_party_id',
        'reason',
        'second_party_id',
        'tax',
        'tax_receiver_id'
    ];

    public function __construct(Token $token, int $page = 1)
    {
        parent::__construct($token);
        $this->page = $page;
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
        if ($this->page) {
            $params['page'] = $this->page;
        }
        $response = $client->invoke("/characters/{$this->getId()}/wallet/journal", $params);
        $pages = $response->get('headers')['X-Pages'][0];
        $journal = collect($response->get('result'));

        $chars = $journal->where('first_party_type', 'character')->pluck('first_party_id');
        $chars = $chars->merge($journal->where('second_party_type', 'character')->pluck('second_party_id'));

        $corps = $journal->where('first_party_type', 'corporation')->pluck('first_party_id');
        $corps = $corps->merge($journal->where('second_party_type', 'corporation')->pluck('second_party_id'));

        $existingChars = Character::select('character_id')->whereIn('character_id',
            $chars)->get()->pluck('character_id');
        $existingCorps = Corporation::select('corporation_id')->whereIn('corporation_id',
            $corps)->get()->pluck('corporation_id');

        $unknownChars = $chars->diff($existingChars);
        $unknownCorps = $corps->diff($existingCorps);

        $knownEntries = CharacterJournalEntry::select('id')->whereIn('id', $journal->pluck('id'))
            ->get()->pluck('id');
        $esiEntries = $journal->pluck('id');
        $unknownEntries = $esiEntries->diff($knownEntries);

        $updateEntries = $journal->whereIn('id', $unknownEntries);
        $time = Carbon::now();
        $keys = collect();
        $updateEntries = $updateEntries->map(function ($entry) use ($time, $keys) {
            $item = array_merge($entry,
                ['created_at' => $time, 'updated_at' => $time, 'date' => Carbon::parse($entry['date'])]);
            return $item;
        });

        $updateEntries = $this->mapRequiredColumns($updateEntries);

        foreach ($unknownChars as $char) {
            dispatch(new CharacterUpdateJob($char));
        }
        foreach ($unknownCorps as $corp) {
            dispatch(new CorporationUpdateJob($corp));
        }

        if ($updateEntries->count()) {

            // CCP stopped returning the type, so we have to manually fetch it...
            $ids = $updateEntries->pluck('first_party_id')->values();
            $ids = $updateEntries->pluck('second_party_id')->values()->merge($ids)->unique()->values();
            $resolvedIds = $this->getClient()->invoke('/universe/names/', [
                RequestOptions::JSON => $ids,
            ], 'POST')->get('result')->keyBy('id');

            $updateEntries = $updateEntries->map(function ($entry) use ($resolvedIds){
                if (isset($entry['first_party_id'])) {
                    $entry['first_party_type'] = $resolvedIds->get($entry['first_party_id'])['category'];
                }
                if (isset($entry['second_party_id'])) {
                    $entry['second_party_type'] = $resolvedIds->get($entry['second_party_id'])['category'];
                }
                return $entry;
            });

            if ($updateEntries->count() && $this->page < $pages) {
                dispatch(new CharacterWalletJournalJob($this->token, $this->page + 1));
            }
            CharacterJournalEntry::insert($updateEntries->toArray());
        }

        $this->logFinished();
    }

}
