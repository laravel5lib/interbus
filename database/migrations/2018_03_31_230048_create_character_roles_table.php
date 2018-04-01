<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCharacterRolesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('character_roles', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('character_id');
            $table->string('role');
            $table->string('location')->nullable();
            $table->timestamps();
            $table->softDeletes();
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
        Schema::dropIfExists('character_roles');
    }
}
