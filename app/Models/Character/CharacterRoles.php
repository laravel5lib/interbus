<?php

namespace App\Models\Character;

use Illuminate\Database\Eloquent\Model;

class CharacterRoles extends Model
{
    protected $guarded = [];

    public function character(){
        return $this->belongsTo(Character::class, 'character_id');
    }
}
