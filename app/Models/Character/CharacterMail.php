<?php

namespace App\Models\Character;

use Illuminate\Database\Eloquent\Model;

class CharacterMail extends Model
{
    public $primaryKey = 'mail_id';

    protected $guarded = [];

    public function recipients() {
        return $this->hasMany(CharacterMailRecipient::class, 'mail_id', 'mail_id');
    }

    public function sender() {
        return $this->hasOne(Character::class, 'character_id', 'from');
    }
}
