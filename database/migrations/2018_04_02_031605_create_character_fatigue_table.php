<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCharacterFatigueTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('character_fatigues', function (Blueprint $table) {
            $table->bigInteger('character_id');
            $table->dateTime('last_jump_date')->nullable();
            $table->dateTime('jump_fatigue_expire_date')->nullable();
            $table->dateTime('last_update_date')->nullable();
            $table->timestamps();
            $table->primary('character_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('character_fatigues');
    }
}
