<?php

namespace App\Providers;

use App\Services\DiscordWebhookService;
use Illuminate\Support\ServiceProvider;

class DiscordWebhookProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(DiscordWebhookService::class, function($app, $channels){

            $channel = 'default';
            if ($channels) {
                $channel = $channels[0];
            }
            $channel = config('discord.channels.' . $channel);
            if (!$channel) {
                throw new \InvalidArgumentException('Invalid Channel!');
            }

            $guzzle = new \GuzzleHttp\Client([
                'base_uri' => $channel,
            ]);
           return new DiscordWebhookService($guzzle);
        });
    }
}
