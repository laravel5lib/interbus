<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUniverseGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('universe_groups', function (Blueprint $table) {
            $table->bigInteger('group_id');
            $table->string('name');
            $table->boolean('published');
            $table->bigInteger('category_id');
            $table->timestamps();
            $table->primary('group_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('universe_groups');
    }
}
