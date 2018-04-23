<?php

namespace App\Models\Character;

use App\Models\Universe\UniverseType;
use Illuminate\Database\Eloquent\Model;

class CharacterCloneImplant extends Model
{
    protected $guarded = [];

    public function clone() {
        return $this->belongsTo(CharacterClone::class);
    }

    public function type() {
        return $this->hasOne(UniverseType::class, 'type_id', 'implant');
    }
}
