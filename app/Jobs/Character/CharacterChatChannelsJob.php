<?php

namespace App\Jobs\Character;

use App\Models\Character\CharacterChatChannel;
use App\Models\Character\CharacterChatChannelsAllowed;
use App\Models\Character\CharacterChatChannelsBlocked;
use App\Models\Character\CharacterChatChannelsMuted;
use App\Models\Character\CharacterChatChannelsOperators;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
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

        foreach ($result as $channel) {
            $channel = collect($channel);

            $allowed = collect($channel->pull('allowed'));
            $operators = collect($channel->pull('operators'));
            $blocked = collect($channel->pull('blocked'));
            $muted = collect($channel->pull('muted'));

            $channel = CharacterChatChannel::updateOrCreate([
                'character_id' => $this->token->character_id,
                'channel_id' => $channel['channel_id']
            ], $channel->toArray()
            );

            $this->updateAllowed($channel, $allowed);
            $this->updateOperators($channel, $operators);
            $this->updateBlocked($channel, $blocked);
            $this->updateMuted($channel, $muted);
        }

        $this->logFinished();
    }

    protected function updateAllowed(CharacterChatChannel $channel, Collection $allowed)
    {
        CharacterChatChannelsAllowed::whereNotIn('accessor_id', $allowed->pluck('accessor_id'))->where('channel_id',
            $channel->channel_id)->delete();
        foreach ($allowed as $allow) {
            CharacterChatChannelsAllowed::updateOrCreate([
                'channel_id' => $channel->channel_id,
                'accessor_id' => $allow['accessor_id']
            ],
                $allow
            );
        }
    }

    protected function updateOperators(CharacterChatChannel $channel, Collection $operators)
    {
        CharacterChatChannelsOperators::whereNotIn('accessor_id', $operators->pluck('accessor_id'))->where('channel_id',
            $channel->channel_id)->delete();
        foreach ($operators as $operator) {
            CharacterChatChannelsOperators::updateOrCreate([
                'channel_id' => $channel->channel_id,
                'accessor_id' => $operator['accessor_id']
            ],
                $operator
            );
        }
    }

    protected function updateBlocked(CharacterChatChannel $channel, Collection $blocked)
    {
        CharacterChatChannelsBlocked::whereNotIn('accessor_id', $blocked->pluck('accessor_id'))->where('channel_id',
            $channel->channel_id)->delete();
        foreach ($blocked as $block) {
            if (isset($block['end_at'])) {
                $block['end_at'] = Carbon::parse($block['end_at']);
            }
            CharacterChatChannelsBlocked::updateOrCreate([
                'channel_id' => $channel->channel_id,
                'accessor_id' => $block['accessor_id']
            ],
                $block
            );
        }
    }

    protected function updateMuted(CharacterChatChannel $channel, Collection $muted)
    {
        CharacterChatChannelsMuted::whereNotIn('accessor_id', $muted->pluck('accessor_id'))->where('channel_id',
            $channel->channel_id)->delete();
        foreach ($muted as $mute) {
            if (isset($mute['end_at'])) {
                $mute['end_at'] = Carbon::parse($mute['end_at']);
            }
            CharacterChatChannelsMuted::updateOrCreate([
                'channel_id' => $channel->channel_id,
                'accessor_id' => $mute['accessor_id']
            ],
                $mute
            );
        }
    }
}
