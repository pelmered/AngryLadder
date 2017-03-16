<?php

use Illuminate\Database\Seeder;
use Faker\Factory AS Faker;

use App\Player;
use App\LadderPeriod;

class LaddersTableSeeder extends Seeder {

    public function run(  )
    {
        $faker = Faker::create();

        $ladders = config('ladder.ladders');

        foreach( $ladders AS $ladderKey => $ladder )
        {
            $ladderPeriod = new LadderPeriod( );

            $enddate = LadderPeriod::getNextEndDate( $ladder );

            $data = [
                'ladder'     => $ladderKey,
                'active'        => 1,
                'period_start'  => LadderPeriod::getCurrentStartDate( $ladder),
                'period_end'    => LadderPeriod::getNextEndDate( $ladder ),
            ];

            LadderPeriod::create($data);
        }
    }
}
