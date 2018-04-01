<?php

namespace App\Console\Commands;

use App\Jobs\Universe\UpdateGroupJob;
use Illuminate\Console\Command;
use tristanpollard\ESIClient\Services\ESIClient;

class UpdateGroups extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'interbus:groups';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update ESI Groups';

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
        $groups = $this->client->invoke('/universe/groups');
        for ($i = 1; $i <= $groups->get('headers')->get('X-Pages')[0]; $i++) {
            $groups = $this->client->invoke('/universe/groups', ['query' => [
                'page' => $i
            ]]);

            $ids = $groups->get('result');
            foreach ($ids as $id) {
                dispatch(new UpdateGroupJob($id));
            }
        }
    }
}
