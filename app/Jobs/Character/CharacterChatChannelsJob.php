<?php

namespace App\Jobs\Character;

use App\Models\Character\CharacterChatChannels;
use tristanpollard\ESIClient\Services\ESIClient;
use App\Jobs\AuthenticatedESIJob;

class CharacterChatChannelsJob extends AuthenticatedESIJob
{
    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->logStart();
        $client = $this->getClient();

        $response = $client->invoke("/characters/{$this->token->character_id}/chat_channels");
        $result = $response->get('result');

        foreach ($result as $channel){
            $channel = collect($channel);
            $allowed = $channel->pull('allowed');
            $operators = $channel->pull('operators');
            $blocked = $channel->pull('blocked');
            $muted = $channel->pull('muted');

            CharacterChatChannels::updateOrCreate([
                    'character_id' => $this->token->character_id, 'channel_id' => $channel['channel_id']
                    ], $channel->toArray()
            )->touch();
         }

        $this->logFinished();
    }
}
