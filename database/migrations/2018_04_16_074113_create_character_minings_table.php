<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCharacterMiningsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('character_minings', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('character_id');
            $table->dateTime('date');
            $table->bigInteger('solar_system_id');
            $table->bigInteger('type_id');
            $table->bigInteger('quantity');
            $table->timestamps();
            //Yea I hate ccp. Legit need every fucking column to uniquely identify....
            $table->index('character_id');
            $table->index('solar_system_id');
            $table->index('date');
            $table->index('type_id');
            $table->index('quantity');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('character_minings');
    }
}
