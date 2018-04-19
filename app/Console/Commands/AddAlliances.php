<?php

namespace App\Console\Commands;

use App\Jobs\AllianceUpdateJob;
use App\Models\Alliance\Alliance;
use Illuminate\Console\Command;
use tristanpollard\ESIClient\Services\ESIClient;

class AddAlliances extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'interbus:alliances';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Pulls and updates all alliances.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(ESIClient $client)
    {
        $response = $client->invoke('/alliances/');
        $alliances = $response->get('result');
        $knownAlliances = Alliance::select('alliance_id')->whereIn('alliance_id', $alliances)->get()->pluck('alliance_id');
        $unknownAlliances = $alliances->diff($knownAlliances);

        foreach ($unknownAlliances as $alliance){
            dispatch(new AllianceUpdateJob($alliance));
        }
    }
}
