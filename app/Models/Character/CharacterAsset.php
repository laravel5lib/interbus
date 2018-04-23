<?php

namespace App\Models\Character;

use App\Models\Universe\UniverseType;
use Illuminate\Database\Eloquent\Model;

class CharacterAsset extends Model
{
    protected $guarded = [];

    public function character() {
        return $this->belongsTo(Character::class, 'character_id', 'character_id');
    }

    public function item() {
        return $this->hasOne(UniverseType::class, 'type_id', 'type_id');
    }

    public function name() {
        return $this->hasOne(CharacterAssetName::class, 'item_id', 'item_id');
    }
}
