<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateContactsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('character_contacts', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('owner_id');
            $table->bigInteger('contact_id');
            $table->double('standing');
            $table->string('contact_type');
            $table->boolean('is_watched')->nullable();
            $table->boolean('is_blocked')->nullable();
            $table->bigInteger('label_id')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index('owner_id');
            $table->index('contact_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('character_contacts');
    }
}
