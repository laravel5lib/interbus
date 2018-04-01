<?php

namespace App\Models\Character;

use App\Models\Universe\UniverseSystem;
use App\Models\Universe\UniverseType;
use Illuminate\Database\Eloquent\Model;

class CharacterMining extends Model
{
    protected $guarded = [];

    public function system() {
        return $this->hasOne(UniverseSystem::class, 'solar_system_id', 'solar_system_id');
    }

    public function type() {
        return $this->hasOne(UniverseType::class, 'type_id', 'type_id');
    }
}
