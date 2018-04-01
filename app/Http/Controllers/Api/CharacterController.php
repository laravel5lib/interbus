<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Alliance\Alliance;
use App\Models\Character\Character;
use App\Models\Corporation\Corporation;
use Illuminate\Http\Request;
use tristanpollard\ESIClient\Services\ESIClient;

class CharacterController extends Controller
{

    public function getCharacters(Request $request) {

        $perPage = $request->get('itemsPerPage') ?? 20;
        $offset = $request->get('offset') ?? 0;
        $chars = \App\Models\Character\Character::orderBy('name',
            'asc')->limit($perPage)->offset($offset)->with('corporation')->with('alliance');

        if ($request->get('q') && strlen($request->get('q')) >= 3) {
            $chars->where('name', 'LIKE', "%{$request->get('q')}%");
            $count = \App\Models\Character\Character::where('name', 'LIKE', "%{$request->get('q')}%")->count();
        } else {
            $count = \App\Models\Character\Character::count();
        }

        $chars = $chars->get();
        return [
            'data' => $chars,
            'count' => $count
        ];
    }

    public function getCharacter($id) {
        $char = \App\Models\Character\Character::where('character_id', $id)
            ->with(['alliance', 'corporation'])->firstOrFail();

        return $char;
    }

    public function getCharacterOnline(Character $character, ESIClient $client) {
        $token = $character->token()->firstOrFail();
        $client->setToken($token);
        $online = $client->invoke('/characters/' . $character->character_id . '/online')->get('result');
        return $online;
    }

    public function getCharacterRoles(Character $character) {
        return $character->roles()->orderBy('location', 'asc')->get();
    }

    public function getCharacterTitles(Character $character) {
        return $character->titles()->orderBy('title_id', 'asc')->get();
    }

    public function getCharacterSkills(Character $character) {
        return $character->skills()->with('skillType')->get();
    }

    public function getCharacterFatigue(Character $id) {
        return $id->fatigue()->first();
    }

    public function getCharacterContacts($id) {
        $contacts = \App\Models\Character\Character::findOrFail($id)->contacts()->orderBy('standing', 'desc')->get();

        $chars = $contacts->where('contact_type', 'character')->pluck('contact_id');
        $corps = $contacts->where('contact_type', 'corporation')->pluck('contact_id');
        $alliances = $contacts->where('contact_type', 'alliance')->pluck('contact_id');

        $chars = Character::whereIn('character_id', $chars)->get();
        $corps = Corporation::whereIn('corporation_id', $corps)->get();
        $alliances = Alliance::whereIn('alliance_id', $alliances)->get();

        foreach ($contacts as $contact) {
            switch ($contact['contact_type']) {
                case 'character':
                    $contact['contact'] = $chars->firstWhere('character_id', $contact['contact_id']);
                    break;
                case 'corporation':
                    $contact['contact'] = $corps->firstWhere('corporation_id', $contact['contact_id']);
                    break;
                case 'alliance':
                    $contact['contact'] = $alliances->firstWhere('alliance_id', $contact['contact_id']);
                    break;
            }
        }

        return $contacts;
    }

    function getCharacterJournal($id) {
        $journal = \App\Models\Character\CharacterJournalEntry::where('first_party_id', $id)->orWhere('second_party_id', $id)->get()->keyBy('ref_id');

        $first = $journal->where('first_party_id', '!=', null)->pluck('first_party_type', 'first_party_id');
        $second = $journal->where('second_party_id', '!=', null)->pluck('second_party_type', 'second_party_id');
        $ids = $first->union($second);

        $chars = $ids->filter(function ($key, $value) {
            if ($key === 'character') {
                return true;
            }
            return false;
        });

        $corps = $ids->filter(function ($key, $value) {
           if ($key === 'corporation') {
               return true;
           }
           return false;
        });

        $corps = Corporation::whereIn('corporation_id', $corps->keys())->get();
        $chars = Character::whereIn('character_id', $chars->keys())->get();

        foreach ($journal as $entry) {
            $first = $entry['first_party_id'];
            $firstType = $entry['first_party_type'];
            $second = $entry['second_party_id'];
            $secondType = $entry['second_party_type'];

            if ($firstType === 'character') {
                $entry['first_party'] = $chars->firstWhere('character_id', $first);
            } else if ($firstType === 'corporation') {
                $entry['first_party'] = $corps->firstWhere('corporation_id', $first);
            }

            if ($secondType === 'character') {
                $entry['second_party'] = $chars->firstWhere('character_id', $second);
            } else if ($secondType === 'corporation') {
                $entry['second_party'] = $corps->firstWhere('corporation_id', $second);
            }
        }

        return $journal->values();
    }

}
