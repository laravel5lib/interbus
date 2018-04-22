<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUniverseMoonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('universe_moons', function (Blueprint $table) {
            $table->bigInteger('moon_id');
            $table->string('name');
            $table->double('x');
            $table->double('y');
            $table->double('z');
            $table->bigInteger('system_id');
            $table->timestamps();
            $table->primary('moon_id');
            $table->index('system_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('universe_moons');
    }
}
