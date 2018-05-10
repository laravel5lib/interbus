<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCorporationStructureServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('corporation_structure_services', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('structure_id');
            $table->string('name');
            $table->string('state');
            $table->timestamps();
            $table->index('structure_id');
            $table->index('name');
            $table->index('state');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('corporation_structure_services');
    }
}
