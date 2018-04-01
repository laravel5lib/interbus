<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCharacterTitlesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('character_titles', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('character_id');
            $table->integer('title_id')->nullable();
            $table->string('name')->nullable();
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
        Schema::dropIfExists('character_titles');
    }
}
