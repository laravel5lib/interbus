<?php

namespace App\Jobs\Character;

use App\Models\Character\CharacterWalletBalance;
use App\Jobs\AuthenticatedESIJob;

class CharacterWalletBalanceJob extends AuthenticatedESIJob{

    protected $scope = 'esi-wallet.read_character_wallet.v1';

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if (!$this->authenticated()){
            return;
        }

        $this->logStart();

        $client = $this->getClient();
        $response = $client->invoke("/characters/{$this->getId()}/wallet");
        $wallet = ['balance' => $response->get('result')->first()];
        CharacterWalletBalance::updateorCreate([
            'character_id' => $this->getId()
            ], $wallet
        );

        $this->logFinished();
    }
}
