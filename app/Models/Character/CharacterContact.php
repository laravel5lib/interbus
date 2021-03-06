<?php

namespace App\Models\Character;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CharacterContact extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    public function contactOwner() {
        return $this->belongsTo(Character::class, 'character_id', 'owner_id');
    }

    public function contact() {
        return $this->morphTo();
    }
}
