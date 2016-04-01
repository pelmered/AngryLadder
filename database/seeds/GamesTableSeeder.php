<?php

use Illuminate\Database\Seeder;
use Faker\Factory AS Faker;

use App\Game;

class GamesTableSeeder extends Seeder {

    
    public function run(  )
    {
        $faker = Faker::create();

        foreach( range(1, 10 ) as $index )
        {
            $data = [
                'player1'   => rand(1,10),
                'player2'   => rand(1,10),
                'score1'    => rand(0,12),
                'score2'    => rand(0,12)
            ];

            $game = Game::create($data);
        }


    }
}