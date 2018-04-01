<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCharacterMedalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('character_medals', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('character_id');
            $table->integer('medal_id');
            $table->string('title');
            $table->text('description');
            $table->bigInteger('corporation_id');
            $table->bigInteger('issuer_id');
            $table->dateTime('date');
            $table->string('reason');
            $table->string('status');
            $table->timestamps();
            $table->index('character_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('character_medals');
    }
}
