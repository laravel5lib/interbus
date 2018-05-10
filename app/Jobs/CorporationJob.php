<?php

namespace App\Jobs;

trait CorporationJob{

    public function getId(): int {
        $corp = $this->token->character()->first()['corporation_id'];
        return $corp;
    }

}