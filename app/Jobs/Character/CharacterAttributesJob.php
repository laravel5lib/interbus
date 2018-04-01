<?php

namespace App\Jobs\Character;

use App\Models\Character\CharacterAttributes;
use Carbon\Carbon;
use tristanpollard\ESIClient\Services\ESIClient;
use App\Jobs\AuthenticatedESIJob;

class CharacterAttributesJob extends AuthenticatedESIJob
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

        $response = $client->invoke("/characters/{$this->token->character_id}/attributes");
        $attributes = $response->get('result');

        if (!empty($attributes['last_remap_date'])){
            $attributes['last_remap_date'] = Carbon::parse($attributes['last_remap_date']);
        }

        if (!empty($attributes['accrued_remap_cooldown_date'])){
            $attributes['accrued_remap_cooldown_date'] = Carbon::parse($attributes['accrued_remap_cooldown_date']);
        }

        CharacterAttributes::updateOrCreate([
            'character_id' => $this->token->character_id
        ], $attributes->toArray()
        )->touch();

        $this->logFinished();
    }
}
