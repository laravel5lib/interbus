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
        $this->app->bind(DiscordWebhookService::class, function($app){
            $guzzle = new \GuzzleHttp\Client([
                'base_uri' => config('discord.discord_webhook_url')
            ]);
           return new DiscordWebhookService($guzzle);
        });
    }
}
