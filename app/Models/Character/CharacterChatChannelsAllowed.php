<?php

namespace App\Models\Character;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CharacterChatChannelsAllowed extends Model
{
    use SoftDeletes;

    protected $table = 'character_chat_channels_allowed';

    protected $guarded = [];
}
