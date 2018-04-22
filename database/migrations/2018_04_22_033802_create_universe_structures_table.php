<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUniverseStructuresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('universe_structures', function (Blueprint $table) {
            $table->bigInteger('structure_id');
            $table->string('name');
            $table->bigInteger('solar_system_id');
            $table->bigInteger('type_id');
            $table->double('x');
            $table->double('y');
            $table->double('z');
            $table->timestamps();
            $table->primary('structure_id');
            $table->index('solar_system_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('universe_structures');
    }
}
