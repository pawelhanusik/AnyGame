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
            
            $table->integer('pos_x');
            $table->integer('pos_y');
            $table->integer('rot_x');
            $table->integer('rot_y');
            $table->integer('rot_z');

            $table->timestamps();

            
            $table->foreign('game_id')->references('id')->on('games')->onDelete('cascade');
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
