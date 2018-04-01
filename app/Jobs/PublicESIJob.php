<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;

class PublicESIJob extends ESIJob
{

    protected $id;

    public function __construct(int $id)
    {
        parent::__construct();
        $this->id = $id;
    }

    public function getId(): Int
    {
        return $this->id;
    }

    protected function logStart(){
        Log::info('Running: ' . get_class($this),
            [
                'job_id' => $this->uid,
                'id' => $this->id
            ]);
    }

    protected function logFinished(){
        Log::info('Finished: ' . get_class($this),
            [
                'job_id' => $this->uid,
                'id' => $this->id
            ]);
    }


    public function shouldDispatch()
    {
        return true;
    }
}