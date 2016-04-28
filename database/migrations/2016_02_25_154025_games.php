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

                $table->tinyInteger('winner', 1)->unsigned()->default(0);
                $table->enum('status', array('challenge', 'accepted', 'finished'))->default('challenge');

                $table->smallInteger('rating_adjustment_player1', 4);
                $table->smallInteger('rating_adjustment_player2', 4);

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
