<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAlliancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('alliances', function (Blueprint $table) {
            $table->bigInteger('alliance_id');
            $table->string('name');
            $table->bigInteger('creator_id');
            $table->bigInteger('creator_corporation_id');
            $table->string('ticker');
            $table->bigInteger('executor_corporation_id')->nullable();
            $table->dateTime('date_founded');
            $table->integer('faction_id')->nullable();
            $table->timestamps();
            $table->primary('alliance_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('alliances');
    }
}
