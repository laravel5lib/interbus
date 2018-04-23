<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCharactersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('characters', function (Blueprint $table){
           $table->bigInteger('character_id');
           $table->string('name');
           $table->text('description')->nullable();
           $table->integer('corporation_id');
           $table->integer('alliance_id')->nullable();
           $table->dateTime('birthday');
           $table->string('gender');
           $table->integer('race_id');
           $table->integer('bloodline_id');
           $table->integer('ancestry_id')->nullable();
           $table->double('security_status')->nullable();
           $table->integer('faction_id')->nullable();
           $table->timestamps();
           $table->primary('character_id');
           $table->index('corporation_id');
           $table->index('alliance_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('characters');
    }
}
