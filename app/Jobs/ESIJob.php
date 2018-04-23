<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use tristanpollard\ESIClient\Services\ESIClient;

abstract class ESIJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $endpoint = "";

    public $version = "";

    protected $uid;

    public $tries = 1;

    public function __construct()
    {
        $this->uid = uniqid('job_');
    }

    public abstract function getId(): Int;

    protected abstract function logStart();

    protected abstract function logFinished();

    public abstract function shouldDispatch();

    protected function getClient(): ESIClient{
        $client = app()->make(ESIClient::class);
        return $client;
    }

}