<?php

namespace App\Jobs;

use App\Models\Token;
use Illuminate\Support\Facades\Log;
use PhpParser\Node\Expr\Cast\Int_;
use tristanpollard\ESIClient\Services\ESIClient;

class AuthenticatedESIJob extends ESIJob{

    protected $token;

    protected $uid;

    protected $scope = "";

    protected $role = "";

    public function __construct(Token $token)
    {
        parent::__construct();
        $this->token = $token;
    }

    public function getId(): Int
    {
        return $this->token->character_id;
    }

    public function getCharacterId(): Int
    {
        return $this->token->character_id;
    }

    protected function authenticated(): bool {

        if ($this->scope) {
            $scopes = $this->token->scopes->pluck('scope');
            if (!$scopes->contains($this->scope)) {
                return false;
            }
        }

        if ($this->role) {
            $roles = $this->token->roles->pluck('role');
            if (!$roles->contains($this->role)) {
                return false;
            }
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
      // $this->token->access_token = 'FrEo7WR0PEeb1ofANo83X2r8vzMP5GyDeRMn2kWycXje9dDxZx8mhRPaEqrO_ftxYd4AZ5I1xpBdw9VforMhDQ2';
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

    public function getScope(): string{
        return $this->scope;
    }

    public function getRole(): string{
        return $this->role;
    }
}