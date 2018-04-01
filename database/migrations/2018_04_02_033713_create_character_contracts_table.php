<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCharacterContractsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('character_contracts', function (Blueprint $table) {
            $table->integer('contract_id');
            $table->bigInteger('issuer_id');
            $table->bigInteger('issuer_corporation_id');
            $table->bigInteger('assignee_id');
            $table->bigInteger('acceptor_id');
            $table->bigInteger('start_location_id')->nullable();
            $table->bigInteger('end_location_id')->nullable();
            $table->string('type');
            $table->string('status');
            $table->string('title')->nullable();
            $table->boolean('for_corporation');
            $table->string('availability');
            $table->dateTime('date_issued');
            $table->dateTime('date_expired');
            $table->dateTime('date_accepted')->nullable();
            $table->integer('days_to_complete')->nullable();
            $table->dateTime('date_completed')->nullable();
            $table->double('price')->nullable();
            $table->double('reward')->nullable();
            $table->double('collateral')->nullable();
            $table->double('buyout')->nullable();
            $table->double('volume')->nullable();
            $table->timestamps();
            $table->primary('contract_id');
            $table->index('assignee_id');
            $table->index('issuer_id');
            $table->index('acceptor_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('character_contracts');
    }
}
