<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCorporationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('corporations', function (Blueprint $table) {
            $table->bigInteger('corporation_id');
            $table->string('name');
            $table->string('ticker');
            $table->integer('member_count');
            $table->bigInteger('ceo_id');
            $table->bigInteger('alliance_id')->nullable();
            $table->text('description')->nullable();
            $table->double('tax_rate');
            $table->dateTime('date_founded')->nullable();
            $table->bigInteger('creator_id');
            $table->string('url')->nullable();
            $table->integer('faction_id')->nullable();
            $table->bigInteger('home_station_id')->nullable();
            $table->bigInteger('shares')->nullable();
            $table->timestamps();
            $table->primary('corporation_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('corporations');
    }
}
