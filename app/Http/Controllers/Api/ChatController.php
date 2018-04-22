<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Character\CharacterChatChannel;

class ChatController extends Controller
{
    public function getChatChannel(CharacterChatChannel $channel) {
        $channel->load('allowed', 'blocked', 'muted', 'operators');
        return $channel;
    }
}
