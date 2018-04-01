<?php

namespace App\Console\Commands;

use App\Jobs\AuthenticatedCharacterUpdateQueuer;
use App\Models\Token;
use Illuminate\Console\Command;

class QueueAuthenticatedCharacters extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'seat:auth';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Queue auth'd jobs";

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
    public function handle()
    {
        $i = 0;

        $tokenCount = Token::count();

        $this->info('Queuing Jobs: ' . $tokenCount . ' tokens');

        Token::chunk(200, function ($tokens) use (&$i, $tokenCount){
            foreach ($tokens as $token){
                dispatch(new AuthenticatedCharacterUpdateQueuer($token));
                $i++;
            }
            $this->info(($i/$tokenCount) * 100 . '% done.');
        });

        $this->info('All Tokens Queued');


    }
}
