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
        $chars = \App\Models\Character\Character::with('corporation', 'alliance');

        if ($request->get('q') && strlen($request->get('q')) >= 3) {
            $chars->where('name', 'LIKE', "%{$request->get('q')}%");
        }

        return $chars->paginate(15);
    }

    public function getCharacterClones(Character $character) {
        return $character->clones()->get();
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
        return $character->mails()->with('sender')->get();
    }

    function getCharacterJournal(Character $character) {
        $journal = \App\Models\Character\CharacterJournalEntry::where('first_party_id', $character->character_id)
            ->orWhere('second_party_id', $character->character_id)
            ->with('firstParty', 'secondParty')
            ->get();

        return $journal;
    }

}
