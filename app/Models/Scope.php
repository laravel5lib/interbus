<?php

namespace App\Models;

use App\Models\Token;
use Illuminate\Database\Eloquent\Model;

class Scope extends Model
{

    protected $guarded = [];

    public $timestamps = false;

    public function token(){
        return $this->belongsTo(Token::class, 'id', 'token');
    }
}
