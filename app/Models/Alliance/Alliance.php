<?php

namespace App\Models\Alliance;

use App\Models\Character\CharacterMailRecipient;
use Illuminate\Database\Eloquent\Model;

class Alliance extends Model
{

    public $primaryKey = 'alliance_id';

    protected $guarded = [];

    public function characters(){
        return $this->hasMany(Character::class, 'alliance_id');
    }

    public function corporations(){
        return $this->hasMany(Corporation::class, 'alliance_id');
    }

    public function mailRecipients() {
        return $this->morphMany(CharacterMailRecipient::class, 'recipient');
    }
}
