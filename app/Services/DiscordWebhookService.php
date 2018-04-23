<?php

namespace App\Services;


use Illuminate\Notifications\Notification;

class DiscordWebhookService
{
    protected $client;

    public function __construct(\GuzzleHttp\Client $guzzle)
    {
        $this->client = $guzzle;
    }

    /**
     * Send the given notification.
     *
     * @param  mixed  $notifiable
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return void
     */
    public function send($notifiable, Notification $notification)
    {
        $message = $notification->toVoice($notifiable);
        $this->sendMessage($message);
    }

    public function sendMessage(string $message) {

        if (strlen($message) > 2000) {
            throw new \InvalidArgumentException("Message is longer than 2000 characters.");
        }

        $this->client->post('', [
            'form_params' => [
                'content' => $message,
            ],
        ]);
    }

}