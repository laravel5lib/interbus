<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class TestCorpJob extends AuthenticatedESIJob
{

    //use CorporationJob;

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {


        $a = $this->getClient()->invoke('/characters/' . $this->getCharacterId() . '/online/');

        $etag = $a->get('Etag');
        if ($this->etagProcessed($etag)) {
            dd("Already done!!!");
        }

        $this->setEtag($etag);
        dd('we made it!');
        return $a;
    }
}
