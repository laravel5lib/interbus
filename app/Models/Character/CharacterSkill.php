<?php

namespace App\Models\Character;

use App\Models\Universe\UniverseType;
use Illuminate\Database\Eloquent\Model;

class CharacterSkill extends Model
{
    protected $guarded = [];

    public function skillType() {
        return $this->hasOne(UniverseType::class, 'type_id', 'skill_id');
    }
}
