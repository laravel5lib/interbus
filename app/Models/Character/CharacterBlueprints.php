<?php

namespace App\Models\Character;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CharacterBlueprints extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    public function character(){
        return $this->belongsTo(Character::class, 'character_id', 'character_id');
    }
}
