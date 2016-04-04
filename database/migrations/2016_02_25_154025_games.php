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
        if(!Schema::hasTable('games')) {
            Schema::create('games', function ($table) {
                $table->increments('id');

                $table->integer('winner')->unsigned();

                $table->integer('rating_adjustment_player1');
                $table->integer('rating_adjustment_player2');

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
