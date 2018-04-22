<?php

namespace App\Models\Character;

use Illuminate\Database\Eloquent\Model;

class CharacterJournalEntry extends Model
{
    public $primaryKey = 'ref_id';

    protected $guarded = [];

    public function firstParty() {
        return $this->morphTo();
    }

    public function secondParty() {
        return $this->morphTo();
    }
}
