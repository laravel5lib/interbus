<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCharacterMailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('character_mails', function (Blueprint $table) {
            $table->bigInteger('mail_id');
            $table->string('subject')->nullable();
            $table->bigInteger('from')->nullable();
            $table->string('from_type')->nullable();
            $table->dateTime('timestamp')->nullable();
            $table->text('body')->nullabe();
            $table->boolean('read')->nullable();
            $table->timestamps();
            $table->primary('mail_id');
            $table->index('from');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('character_mails');
    }
}
