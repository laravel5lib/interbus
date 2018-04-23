<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCharacterAssetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('character_assets', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('character_id');
            $table->bigInteger('type_id');
            $table->bigInteger('quantity');
            $table->bigInteger('location_id');
            $table->string('location_type');
            $table->bigInteger('item_id');
            $table->string('location_flag');
            $table->boolean('is_singleton');
            $table->timestamps();
            $table->index('character_id');
            $table->index('type_id');
            $table->index('location_id');
            $table->index('location_flag');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('character_assets');
    }
}
