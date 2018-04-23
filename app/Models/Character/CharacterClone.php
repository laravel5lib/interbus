<?php

namespace App\Models\Character;

use Illuminate\Database\Eloquent\Model;

class CharacterClone extends Model
{
    public $primaryKey = 'jump_clone_id';

    protected $guarded = [];

    public function implants() {
        return $this->hasMany(CharacterCloneImplant::class, 'clone_id', 'jump_clone_id');
    }

    public function location() {
        return $this->morphTo();
    }
}
