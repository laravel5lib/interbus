<?php

namespace App\Models\Observers;

use App\Jobs\Character\CharacterUpdateJob;
use App\Models\Token;

class TokenObserver
{
    public function created(Token $token){
        dispatch(new CharacterUpdateJob($token->character_id));
    }
}