<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class PlayerRatingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('player_ratings', function ($table) {
            $table->increments('id');

            $table->string('ladder', 50);
            $table->integer('player_id')->unsigned();
            $table->decimal('rating', 10 );
            $table->decimal('rating_mu', 10 );
            $table->decimal('rating_deviation', 10 );
            $table->decimal('rating_deviation_phi', 10 );
            $table->decimal('rating_volatility', 10 );

            $table->timestamps();

            $table->index(['player_id', 'ladder']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('player_ratings');
    }
}
