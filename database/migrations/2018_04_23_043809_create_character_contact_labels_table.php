<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCharacterContactLabelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('character_contact_labels', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('character_id');
            $table->bigInteger('label_id');
            $table->string('label_name');
            $table->timestamps();
            $table->index('character_id');
            $table->index('label_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('character_contact_labels');
    }
}
