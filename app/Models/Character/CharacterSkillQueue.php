<?php

namespace App\Models\Character;

use App\Models\Universe\UniverseType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CharacterSkillQueue extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    public function type() {
        return $this->hasOne(UniverseType::class, 'type_id', 'skill_id');
    }
}
