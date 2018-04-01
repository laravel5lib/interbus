<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCharacterStandingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('character_standings', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('character_id');
            $table->biginteger('from_id');
            $table->string('from_type');
            $table->double('standing');
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
        Schema::dropIfExists('character_standings');
    }
}
