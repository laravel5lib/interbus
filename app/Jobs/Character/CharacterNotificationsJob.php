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
        $notifications = $response->get('result')->keyBy('notification_id');

        $knownNotifications = CharacterNotification::select('notification_id', 'sender_id', 'sender_type', 'timestamp', 'is_read', 'text', 'type')->where('character_id', $this->getId())
            ->whereIn('notification_id', $notifications->pluck('notification_id'))
            ->get();

        foreach ($knownNotifications as $dbNotification) {
            $knownNotification = collect($dbNotification);
            $knownNotification = $knownNotification->filter(function ($notification){
                return $notification !== null;
            });
            $esiNotification = $notifications[$knownNotification['notification_id']];
            $esiNotification['timestamp'] = Carbon::parse($esiNotification['timestamp']);
            $knownNotification['timestamp'] = Carbon::parse($knownNotification['timestamp']);
            if ($esiNotification != $knownNotification->toArray()) {
                $dbNotification->fill($esiNotification);
                $dbNotification->save();
            }
            $notifications->forget($knownNotification['notification_id']);
        }

        if ($notifications->count()) {
            $time = Carbon::now();
            $notifications = $notifications->map(function ($notification) use ($time) {
                $notification['timestamp'] = Carbon::parse($notification['timestamp']);
               return array_merge(['character_id' => $this->getId(), 'is_read' => null, 'created_at' => $time, 'updated_at' => $time], $notification);
            });
            CharacterNotification::insert($notifications->toArray());
        }

        $this->logFinished();
    }
}
