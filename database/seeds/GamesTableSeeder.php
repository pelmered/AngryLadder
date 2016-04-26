<?php

use Illuminate\Database\Seeder;
use Faker\Factory AS Faker;

use App\Game;
use App\Player;
use App\Set;

class GamesTableSeeder extends Seeder {

    
    public function run(  )
    {
        $faker = Faker::create();

        foreach( range(1, 50 ) as $index )
        {

            $playerData = [
                Player::find(rand(1, 10)),
                Player::find(rand(1, 10))
            ];

            $scoreData = [];

            for( $i=0; $i < 3;  $i++)
            {
                $set = [
                    rand(0, 9),
                    11
                ];

                shuffle($set);

                $scoreData[] = [
                    'set' => $set
                ];
            }

            $game = Game::createNewGame( $playerData, $scoreData );
        }


    }
}