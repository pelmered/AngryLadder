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

                $table->tinyInteger('winner')->unsigned()->default(0);;
                $table->enum('status', array('challenge', 'accepted', 'completed', 'confirmed'))->default('challenge');

                $table->decimal('rating_adjustment_player1');
                $table->decimal('rating_adjustment_player2');

                $table->timestamps();


                $table->index(['winner', 'status']);
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
