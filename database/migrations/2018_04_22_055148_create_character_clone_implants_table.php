<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCharacterCloneImplantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('character_clone_implants', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('clone_id');
            $table->bigInteger('implant');
            $table->timestamps();
            $table->index('clone_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('character_clone_implants');
    }
}
