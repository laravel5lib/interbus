<?php

namespace App\Listeners;

use App\Events\TokenInvalidated;
use App\Services\DiscordWebhookService;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class TokenInvalidatedListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  TokenInvalidated  $event
     * @return void
     */
    public function handle(TokenInvalidated $event)
    {
        /** @var DiscordWebhookService $discord */
        $discord = app()->make(DiscordWebhookService::class);
        $discord->sendMessage("{$event->token->character_name} ({$event->token->character_id}) Removed their token!");
        $event->token->delete();
    }
}
