<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCharacterChatChannelsAllowedTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('character_chat_channels_allowed', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('channel_id');
            $table->bigInteger('accessor_id');
            $table->string('accessor_type');
            $table->timestamps();
            $table->index('channel_id');
            $table->index('accessor_id');
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('character_chat_channels_allowed');
    }
}
