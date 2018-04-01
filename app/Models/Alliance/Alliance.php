<?php

namespace App\Models\Alliance;

use Illuminate\Database\Eloquent\Model;

class Alliance extends Model
{

    public $primaryKey = 'alliance_id';

    protected $guarded = [];

    public function characters(){
        return $this->hasMany(Character::class, 'alliance_id');
    }

    public function corporations(){
        return $this->hasMany(Corporation::class, 'alliance_id');
    }

}
