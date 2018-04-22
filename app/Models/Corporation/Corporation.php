<?php

namespace App\Models\Corporation;

use App\Models\Character\CharacterMailRecipient;
use Illuminate\Database\Eloquent\Model;

class Corporation extends Model
{

    public $primaryKey = 'corporation_id';

    protected $guarded = [];

    public function alliance(){
        return $this->belongsTo(Alliance::class, 'alliance_id', 'alliance_id');
    }

    public function characters(){
        return $this->hasMany(Character::class, 'corporation_id');
    }

    public function history(){
        return $this->hasMany(CorporationHistory::class, 'corporation_id');
    }

    public function mailRecipients() {
        return $this->morphMany(CharacterMailRecipient::class, 'recipient');
    }
}
