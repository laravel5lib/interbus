<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCharacterNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('character_notifications', function (Blueprint $table) {
            $table->bigInteger('notification_id');
            $table->bigInteger('character_id');
            $table->bigInteger('sender_id');
            $table->string('sender_type');
            $table->dateTime('timestamp');
            $table->boolean('is_read')->nullable();
            $table->text('text')->nullable();
            $table->string('type');
            $table->timestamps();
            $table->primary('notification_id');
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
        Schema::dropIfExists('character_notifications');
    }
}
