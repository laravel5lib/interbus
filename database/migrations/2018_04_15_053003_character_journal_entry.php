<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CharacterJournalEntry extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('character_journal_entries', function (Blueprint $table) {
            $table->bigInteger('ref_id');
            $table->dateTime('date');
            $table->string('ref_type');
            $table->bigInteger('first_party_id')->nullable();
            $table->string('first_party_type')->nullable();
            $table->bigInteger('second_party_id')->nullable();
            $table->string('second_party_type')->nullable();
            $table->double('amount')->nullable();
            $table->double('balance')->nullable();
            $table->string('reason')->nullable();
            $table->bigInteger('tax_reciever_id')->nullable();
            $table->double('tax')->nullable();
            $table->timestamps();
            $table->primary('ref_id');
            $table->index('first_party_id');
            $table->index('second_party_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('character_journal_entries');
    }
}
