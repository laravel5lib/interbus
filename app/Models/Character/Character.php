<?php

namespace App\Models\Character;

use App\Models\Alliance\Alliance;
use App\Models\Corporation\Corporation;
use App\Models\Token;
use Illuminate\Database\Eloquent\Model;

class Character extends Model
{
    public $primaryKey = 'character_id';

    protected $guarded = [];

    public function corporation(){
        return $this->belongsTo(Corporation::class, 'corporation_id', 'corporation_id');
    }

    public function alliance() {
        return $this->belongsTo(Alliance::class, 'alliance_id', 'alliance_id');
    }

    public function contacts(){
        return $this->hasMany(CharacterContact::class, 'owner_id');
    }

    public function contactOf(){
        return $this->hasMany(CharacterContact::class, 'contact_id');
    }

    public function roles(){
        return $this->hasMany(CharacterRoles::class, 'character_id');
    }

    public function fatigue() {
        return $this->hasOne(CharacterFatigue::class, 'character_id');
    }

    public function skills() {
        return $this->hasMany(CharacterSkill::class, 'character_id');
    }

    public function titles() {
        return $this->hasMany(CharacterTitles::class, 'character_id');
    }

    public function token(){
        return $this->hasOne(Token::class, 'character_id');
    }

    public function walletBalance() {
        return $this->hasOne(CharacterWalletBalance::class, 'character_id');
    }

}