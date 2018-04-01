<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCharacterAttributesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('character_attributes', function (Blueprint $table) {
            $table->bigInteger('character_id');
            $table->integer('charisma');
            $table->integer('intelligence');
            $table->integer('willpower');
            $table->integer('memory');
            $table->integer('perception');
            $table->integer('bonus_remaps')->nullable();
            $table->dateTime('last_remap_date')->nullable();
            $table->dateTime('accrued_remap_cooldown_date')->nullable();
            $table->timestamps();
            $table->primary('character_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('character_attributes');
    }
}
