<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUniverseStationServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('universe_station_services', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('station_id');
            $table->string('service');
            $table->timestamps();
            $table->index('station_id');
            $table->index('service');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('universe_station_services');
    }
}
