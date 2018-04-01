<?php

namespace App\Jobs;

use App\Models\Token;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class AuthenticatedCharacterUpdateQueuer implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $token;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Token $token)
    {
        $this->token = $token;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $authenticatedCharacterJobs = config('app.authenticatedJobs.character');

        foreach ($authenticatedCharacterJobs as $authenticatedCharacterJob){
            /** @var AuthenticatedESIJob $job */
            $job = new $authenticatedCharacterJob($this->token);
            if ($job->shouldDispatch()) {
                dispatch($job);
            }
        }
    }
}
