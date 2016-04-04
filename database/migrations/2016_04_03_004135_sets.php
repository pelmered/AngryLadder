<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Sets extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('sets')) {
            Schema::create('sets', function ($table) {
                $table->increments('id');

                $table->integer('game_id')->unsigned();

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
        Schema::dropIfExists('sets');
    }
}
