<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCharacterBlueprintsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('character_blueprints', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('character_id');
            $table->bigInteger('item_id');
            $table->integer('type_id');
            $table->bigInteger('location_id');
            $table->string('location_flag');
            $table->integer('quantity');
            $table->integer('time_efficiency');
            $table->integer('material_efficiency');
            $table->integer('runs');
            $table->softDeletes();
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
        Schema::dropIfExists('character_blueprints');
    }
}
