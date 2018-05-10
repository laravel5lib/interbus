<?php

namespace App;

use App\Models\Corporation\CorporationStructureService;
use Illuminate\Database\Eloquent\Model;

class CorporationStructure extends Model
{
    public $primaryKey = 'structure_id';

    protected $guarded = [];

    public function services() {
        return $this->hasMany(CorporationStructureService::class, 'structure_id');
    }
}
