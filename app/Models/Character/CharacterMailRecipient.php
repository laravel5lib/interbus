<?php

namespace App\Models\Character;

use Illuminate\Database\Eloquent\Model;

class CharacterMailRecipient extends Model
{
    public $timestamps = false;

    protected $guarded = [];

    public function mail() {
        return $this->belongsTo(CharacterMail::class, 'mail_id', 'mail_id');
    }

    public function recipient() {
        return $this->morphTo();
    }
}
