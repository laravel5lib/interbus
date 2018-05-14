<?php

namespace App\Models;

use App\Models\Character\Character;
use App\Models\Character\CharacterRoles;
use App\Models\Scope;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use tristanpollard\ESIClient\Services\SSO;

class Token extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    public function character() {
        return $this->belongsTo(Character::class, 'character_id', 'character_id');
    }

    public function scopes(){
        return $this->hasMany(Scope::class, 'token');
    }

    public function roles() {
        return $this->hasManyThrough(CharacterRoles::class, Character::class, 'character_id', 'character_id', 'character_id');
    }

    public function authorization(){
        return 'Bearer ' . $this->access_token;
    }

    public function refresh() {
        /** @var SSO $sso */
        $sso = app()->make(SSO::class);
        $token = $sso->refreshToken($this->refresh_token);
        $token->pull('token_type');
        $token->put('expires', Carbon::now()->addSeconds($token->pull('expires_in')));
        $this->fill($token->toArray());
        $this->save();
    }
}
