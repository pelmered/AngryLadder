<?php

use Illuminate\Database\Seeder;
use Faker\Factory AS Faker;


use App\Player;
use App\LadderPeriod;
use App\PlayerRating;

class PlayersTableSeeder extends Seeder {


    public function run(  )
    {
        $faker = Faker::create();

        $ladders = config('ladder.ladders');
        $ladderConfig = config('ladder');

        foreach( range(1, 10 ) as $index )
        {
            $firstName = $faker->firstName();
            $lastName = $faker->lastName();

            //DB::table('players')->insert([
            $player = Player::create([
                'name'          => $firstName . ' ' . $lastName,
                'email'         => $faker->email,
                'avatar_url'    => $faker->imageUrl(200, 200, 'cats'),
                'slack_id'      => $faker->numberBetween(10000, 999999),
                'slack_name'    => strtolower($firstName),
                //'rating'        => 1000,
                'added_from'    => 'seed'
            ]);


            foreach( $ladderConfig['ladders'] AS $ladder_id => $ladder )
            {

                DB::enableQueryLog();

                //$ladderPeriod = new LadderPeriod( $ladder );

                //$currentPeriod = $ladderPeriod->getCurrent();

                $rating = new App\PlayerRating();

                //$rating = $player->getRating( $ladder_id );
                $rating->ladder = $ladder_id;
                $rating->player_id = $ladder_id;

                /*

                echo get_class($rating);
                die();
                */
                //dd(DB::getQueryLog());
                /*
                $rating->ladder = $ladder_id;
                $rating->rating = $ladderConfig['settings']['start_rating'];
                $rating->rating = $ladderConfig['settings']['start_rating'];
                $rating->rating = $ladderConfig['settings']['start_rating'];
                $rating->rating = $ladderConfig['settings']['start_rating'];
                */

                //print_r($rating);
                $rating->init();

                print_r($rating);

                //dd($rating);
                $player->rating()->save($rating);

                //$rating->save();

            }


        }

    }
}
