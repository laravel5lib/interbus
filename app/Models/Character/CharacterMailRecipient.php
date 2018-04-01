<?php

namespace App\Models\Character;

use Illuminate\Database\Eloquent\Model;

class CharacterMailRecipient extends Model
{
    protected $guarded = [];

    public function mail() {
        return $this->belongsTo(CharacterMail::class, 'mail_id', 'mail_id');
    }
}
