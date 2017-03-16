<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Matches extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('matches')) {
            Schema::create('matches', function ($table) {
                $table->increments('id');

                $table->tinyInteger('winner')->unsigned()->default(0);;
                $table->enum('status', array('challenge', 'accepted', 'completed', 'confirmed'))->default('challenge');

                $table->decimal('rating_adjustment_player1');
                $table->decimal('rating_adjustment_player2');

                $table->integer('player1_id')->unsigned();
                $table->integer('player2_id')->unsigned();

                $table->timestamps();

                //$table->index(['winner', 'status']);
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
        Schema::dropIfExists('matches');
    }
}
