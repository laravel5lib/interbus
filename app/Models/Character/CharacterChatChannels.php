<?php

namespace App\Models\Character;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CharacterChatChannels extends Model
{

    use SoftDeletes;

    protected $guarded = [];

}
