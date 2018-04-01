<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCharacterMailRecipientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('character_mail_recipients', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('mail_id');
            $table->bigInteger('recipient_id');
            $table->string('recipient_type');
            $table->timestamps();
            $table->index('recipient_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('character_mail_recipients');
    }
}
