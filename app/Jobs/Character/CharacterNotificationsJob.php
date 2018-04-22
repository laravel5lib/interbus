<?php

namespace App\Jobs\Character;

use App\Jobs\AuthenticatedESIJob;
use App\Models\Character\CharacterNotification;
use Carbon\Carbon;

class CharacterNotificationsJob extends AuthenticatedESIJob
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
        $response = $client->invoke("/characters/{$this->getId()}/notifications");
        $notifications = $response->get('result');

        DB::transaction(function ($db) use ($notifications) {
            foreach ($notifications as $notification) {
                $notification['timestamp'] = Carbon::parse($notification['timestamp']);
                CharacterNotification::updateOrCreate([
                    'character_id' => $this->getId(),
                    'notification_id' => $notification['notification_id'],
                ], $notification
                );
            }
        });

        $this->logFinished();
    }
}
