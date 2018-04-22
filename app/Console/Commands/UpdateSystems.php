<?php

namespace App\Console\Commands;

use App\Jobs\Universe\UpdateSystemJob;
use Illuminate\Console\Command;
use tristanpollard\ESIClient\Services\ESIClient;

class UpdateSystems extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'interbus:systems';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update systems';

    protected $client;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(ESIClient $client)
    {
        parent::__construct();
        $this->client = $client;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $systems = $this->client->invoke('/universe/systems')->get('result');

        foreach ($systems as $system) {
            dispatch(new UpdateSystemJob($system));
        }
    }
}
