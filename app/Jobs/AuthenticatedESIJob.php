<?php

namespace App\Jobs;

use App\Models\Token;
use Illuminate\Support\Facades\Log;
use tristanpollard\ESIClient\Services\ESIClient;

class AuthenticatedESIJob extends ESIJob{

    protected $token;

    protected $uid;

    protected $scope = "";

    public function __construct(Token $token)
    {
        parent::__construct();
        $this->token = $token;
    }

    public function getId(): Int
    {
        return $this->token->character_id;
    }

    protected function authenticated(): bool {
        $scopes = $this->token->scopes->pluck('scope');
        dd($scopes);
        if (!$scopes->contains($this->scope) && $this->scope){
            return false;
        }

        return true;
    }

    protected function logStart(){
        Log::info('Running: ' . get_class($this),
            [
                'job_id' => $this->uid,
                'token' => $this->token->id,
                'character' => $this->token->character_id
            ]);
    }

    protected function logFinished(){
        Log::info('Finished: ' . get_class($this),
            [
                'job_id' => $this->uid,
                'token' => $this->token->id,
                'character' => $this->token->character_id
            ]);
    }

    protected function getClient(): ESIClient
    {
       $client = parent::getClient();
       $client->setToken($this->token);
       return $client;
    }

    protected function cacheKey(): string {
        return get_called_class() . ':' . $this->token->id;
    }

    public function shouldDispatch()
    {
        return $this->authenticated();
    }
}