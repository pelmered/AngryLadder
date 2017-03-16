<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class LadderPeriodsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ladder_periods', function (Blueprint $table) {
            $table->increments('id');

            $table->string('ladder', 255)->default('alltime');

            $table->tinyInteger('active')->unsigned()->default(1);

            $table->dateTime('period_start');
            $table->dateTime('period_end');

            $table->timestamps();
            $table->softDeletes();

            $table->index(['active', 'period_start', 'period_end']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('ladder_periods');
    }
}
