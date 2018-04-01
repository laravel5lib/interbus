<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCorporationsHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('corporation_histories', function (Blueprint $table) {
            $table->integer('record_id');
            $table->bigInteger('corporation_id');
            $table->date('start_date');
            $table->bigInteger('alliance_id')->nullable();
            $table->boolean('is_deleted')->default(false);
            $table->timestamps();
            $table->primary('record_id');
            $table->index('corporation_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('corporation_histories');
    }
}
