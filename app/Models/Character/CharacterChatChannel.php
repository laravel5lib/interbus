<?php

namespace App\Models\Character;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CharacterChatChannel extends Model
{

    use SoftDeletes;

    protected $guarded = [];

    public function allowed() {
        return $this->hasMany(CharacterChatChannelsAllowed::class, 'channel_id', 'channel_id');
    }

    public function blocked() {
        return $this->hasMany(CharacterChatChannelsBlocked::class, 'channel_id', 'channel_id');
    }

    public function muted() {
        return $this->hasMany(CharacterChatChannelsMuted::class, 'channel_id', 'channel_id');
    }

    public function operators() {
        return $this->hasMany(CharacterChatChannelsOperators::class, 'channel_id', 'channel_id');
    }

}
