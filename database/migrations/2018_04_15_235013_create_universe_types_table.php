<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUniverseTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('universe_types', function (Blueprint $table) {
            $table->bigInteger('type_id');
            $table->string('name');
            $table->text('description');
            $table->boolean('published');
            $table->bigInteger('group_id');
            $table->bigInteger('market_group_id')->nullable();
            $table->double('radius')->nullable();
            $table->double('volume')->nullable();
            $table->double('packaged_volume')->nullable();
            $table->bigInteger('icon_id')->nullable();
            $table->double('capacity')->nullable();
            $table->bigInteger('portion_size')->nullable();
            $table->double('mass')->nullable();
            $table->bigInteger('graphic_id')->nullable();
            $table->timestamps();
            $table->primary('type_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('universe_types');
    }
}
