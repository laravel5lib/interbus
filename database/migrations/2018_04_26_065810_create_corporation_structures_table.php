<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCorporationStructuresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('corporation_structures', function (Blueprint $table) {
            $table->bigInteger('structure_id');
            $table->bigInteger('corporation_id');
            $table->dateTime('fuel_expires')->nullable();
            $table->dateTime('next_reinforce_apply')->nullable();
            $table->integer('next_reinforce_hour')->nullable();
            $table->integer('next_reinforce_weekday')->nullable();
            $table->bigInteger('profile_id');
            $table->integer('reinforce_hour');
            $table->integer('reinforce_weekday');
            $table->string('state');
            $table->dateTime('state_timer_end')->nullable();
            $table->dateTime('state_timer_start')->nullable();
            $table->bigInteger('system_id');
            $table->bigInteger('type_id');
            $table->dateTime('unanchors_at')->nullable();
            $table->timestamps();
            $table->primary('structure_id');
            $table->index('corporation_id');
            $table->index('system_id');
            $table->index('type_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('corporation_structures');
    }
}
