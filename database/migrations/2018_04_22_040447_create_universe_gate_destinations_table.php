<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUniverseGateDestinationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('universe_gate_destinations', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('stargate_id');
            $table->bigInteger('destination_stargate_id');
            $table->bigInteger('destination_system_id');
            $table->timestamps();
            $table->index('stargate_id');
            $table->index('destination_system_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('universe_gate_destinations');
    }
}
