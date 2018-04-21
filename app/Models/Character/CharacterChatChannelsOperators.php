<?php

namespace App\Models\Character;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CharacterChatChannelsOperators extends Model
{
    use SoftDeletes;

    protected $table = 'character_chat_channels_operators';

    protected $guarded = [];
}
