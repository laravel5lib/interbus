<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUniverseGatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('universe_gates', function (Blueprint $table) {
            $table->bigInteger('stargate_id');
            $table->string('name');
            $table->bigInteger('type_id');
            $table->double('x');
            $table->double('y');
            $table->double('z');
            $table->bigInteger('system_id');
            $table->timestamps();
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
        Schema::dropIfExists('universe_gates');
    }
}
