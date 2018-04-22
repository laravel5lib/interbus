<?php

namespace App\Models\Universe;

use App\UniverseStationService;
use Illuminate\Database\Eloquent\Model;

class UniverseStation extends Model
{
    public $primaryKey = 'station_id';

    protected $guarded = [];

    public function services() {
        return $this->hasMany(UniverseStationService::class, 'station_id', 'station_id');
    }
}
