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
        $chars = \App\Models\Character\Character::with('corporation', 'alliance')->orderBy('name', 'asc');

        if ($request->get('q') && strlen($request->get('q')) >= 3) {
            $chars->where('name', 'LIKE', "%{$request->get('q')}%");
        }

        return $chars->paginate(15);
    }

    public function getCharacterAssets(Character $character) {
        return $character->assets()->where('location_flag', 'Hangar')->with('name', 'item')->paginate(50);
    }

    public function getCharacterSkillQueue(Character $character) {
        return $character->skillQueue()->with('type')->get();
    }

    public function getCharacterAttributes(Character $character) {
        return $character->attributes()->first();
    }

    public function getCharacterClones(Character $character) {
        return $character->clones()->with('implants', 'implants.type', 'location')->get();
    }

    public function getCharacterChatChannels(Character $character) {
        return $character->chatChannels()->with('owner')->get();
    }

    public function getCharacter(Character $character) {
        $character->load('corporation', 'alliance');
        return $character;
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
        return $character->skills()->with('skillType', 'skillType.group')->get();
    }

    public function getCharacterFatigue(Character $character) {
        return $character->fatigue()->first();
    }

    public function getCharacterContacts(Character $character) {
        $contacts = $character->contacts()->with('contact')->get();
        return $contacts;
    }

    function getCharacterMail(Character $character) {
        return $character->mails()->with('sender')->orderBy('timestamp', 'desc')->paginate(15);
    }

    function getCharacterJournal(Character $character) {
        $journal = \App\Models\Character\CharacterJournalEntry::where('first_party_id', $character->character_id)
            ->orWhere('second_party_id', $character->character_id)
            ->with('firstParty', 'secondParty')
            ->get();

        return $journal;
    }

}
