<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Players extends Migration
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
        if(!Schema::hasTable('players')) {

            Schema::create('players', function ($table) {
                $table->increments('id');

                $table->string('name');
                $table->string('email');
                $table->string('avatar_url');
                $table->string('slack_id', 20);
                $table->string('slack_name', 50);

                $table->decimal('rating', 10 )->default(1000);
                $table->decimal('rating_weekly', 10 )->default(1000);


                $table->string('added_from', 20);

                $table->timestamps();
                $table->softDeletes();
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
        Schema::dropIfExists('players');
    }
}
