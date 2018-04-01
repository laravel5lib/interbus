<?php

namespace App\Models\Universe;

use Illuminate\Database\Eloquent\Model;

class UniverseGroup extends Model
{
    public $primaryKey = 'group_id';

    protected $guarded = [];

    public function types() {
        return $this->hasMany(UniverseType::class, 'group_id');
    }
}
