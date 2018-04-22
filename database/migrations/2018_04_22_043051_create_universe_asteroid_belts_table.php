<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUniverseAsteroidBeltsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('universe_asteroid_belts', function (Blueprint $table) {
            $table->bigInteger('asteroid_belt_id');
            $table->string('name');
            $table->double('x');
            $table->double('y');
            $table->double('z');
            $table->bigInteger('system_id');
            $table->bigInteger('planet_id');
            $table->timestamps();
            $table->primary('asteroid_belt_id');
            $table->index('planet_id');
            $table->index('system_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('universe_asteroid_belts');
    }
}
