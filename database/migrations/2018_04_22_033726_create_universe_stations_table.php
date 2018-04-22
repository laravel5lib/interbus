<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUniverseStationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('universe_stations', function (Blueprint $table) {
            $table->bigInteger('station_id');
            $table->string('name');
            $table->bigInteger('owner')->nullable();
            $table->bigInteger('type_id');
            $table->bigInteger('race_id')->nullable();
            $table->double('x');
            $table->double('y');
            $table->double('z');
            $table->bigInteger('system_id');
            $table->double('reprocessing_efficiency');
            $table->double('reprocessing_stations_take');
            $table->double('max_dockable_ship_volume');
            $table->double('office_rental_cost');
            $table->timestamps();
            $table->primary('station_id');
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
        Schema::dropIfExists('universe_stations');
    }
}
