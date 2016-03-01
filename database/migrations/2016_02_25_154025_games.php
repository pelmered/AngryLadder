<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Games extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /**
         * Main plugins table
         */
        if(!Schema::hasTable('games')) {
            Schema::create('games', function ($table) {
                $table->increments('id');

                $table->integer('player1')->unsigned();
                $table->integer('player2')->unsigned();

                $table->integer('score1')->unsigned();
                $table->integer('score2')->unsigned();

                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('games');
    }
}
