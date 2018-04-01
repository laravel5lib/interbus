<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCharacterClonesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('character_clones', function (Blueprint $table) {
            $table->integer('jump_clone_id');
            $table->bigInteger('character_id');
            $table->string('name')->nullable();
            $table->bigInteger('location_id');
            $table->string('location_type');
            $table->timestamps();
            $table->primary('jump_clone_id');
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
        Schema::dropIfExists('character_clones');
    }
}
