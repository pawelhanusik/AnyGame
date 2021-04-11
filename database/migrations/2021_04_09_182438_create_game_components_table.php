<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGameComponentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('game_components', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('game_id');
            $table->morphs("game_componentable");

            // tells who has the component in theirs hand (null if on table)
            $table->unsignedBigInteger('owner_id')->nullable();
            // tells who is allowed to edit the component
            $table->unsignedBigInteger('editor_id')->nullable();
            
            // component's positions
            $table->integer('pos_x');
            $table->integer('pos_y');
            // [0; 5] -> tells which side of the component is on top
            $table->integer('orientation');

            $table->timestamps();

            
            $table->foreign('game_id')->references('id')->on('games')->onDelete('cascade');
            $table->foreign('owner_id')->references('id')->on('players')->onDelete('set null');
            $table->foreign('editor_id')->references('id')->on('players')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('game_components');
    }
}
