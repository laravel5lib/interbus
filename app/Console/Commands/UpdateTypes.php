<?php

namespace App\Console\Commands;

use App\Jobs\Universe\UpdateTypeJob;
use Illuminate\Console\Command;
use tristanpollard\ESIClient\Services\ESIClient;

class UpdateTypes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'interbus:types';

    protected $client;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Updates Types';

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
        $types = $this->client->invoke('/universe/types');
        for ($i = 1; $i <= $types->get('headers')->get('X-Pages')[0]; $i++) {
            $types = $this->client->invoke('/universe/types', ['query' => [
                'page' => $i
            ]]);

            $ids = $types->get('result');
            foreach ($ids as $id) {
                dispatch(new UpdateTypeJob($id));
            }
        }
    }
}
