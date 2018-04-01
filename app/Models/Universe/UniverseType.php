<?php

namespace App\Models\Universe;

use Illuminate\Database\Eloquent\Model;

class UniverseType extends Model
{
    public $primaryKey = 'type_id';

    protected $guarded = [];

    public function group() {
        return $this->belongsTo(UniverseGroup::class, 'group_id', 'group_id');
    }
}
