<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCharacterChatChannelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('character_chat_channels', function (Blueprint $table) {
            $table->bigInteger('character_id');
            $table->bigInteger('channel_id');
            $table->string('name');
            $table->integer('owner_id');
            $table->string('comparison_key');
            $table->boolean('has_password');
            $table->text('motd');
            $table->softDeletes();
            $table->timestamps();
            $table->primary('channel_id');
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
        Schema::dropIfExists('character_chat_channels');
    }
}
