<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUniverseSystemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('universe_systems', function (Blueprint $table) {
            $table->bigInteger('system_id');
            $table->bigInteger('star_id');
            $table->string('name');
            $table->double('x');
            $table->double('y');
            $table->double('z');
            $table->double('security_status');
            $table->string('security_class')->nullable();
            $table->bigInteger('constellation_id');
            $table->timestamps();
            $table->primary('system_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('universe_systems');
    }
}
