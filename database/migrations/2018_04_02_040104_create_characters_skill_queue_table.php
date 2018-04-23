<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCharactersSkillQueueTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('character_skill_queues', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('character_id');
            $table->integer('skill_id');
            $table->dateTime('finish_date')->nullable();
            $table->dateTime('start_date')->nullable();
            $table->integer('finished_level');
            $table->integer('queue_position');
            $table->integer('training_start_sp')->nullable();
            $table->integer('level_end_sp')->nullable();
            $table->integer('level_start_sp')->nullable();
            $table->softDeletes();
            $table->timestamps();
            $table->index('character_id');
            $table->index('finished_level');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('character_skill_queues');
    }
}
