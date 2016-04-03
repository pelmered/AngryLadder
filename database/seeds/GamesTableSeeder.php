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

        foreach( range(1, 10 ) as $index )
        {
            $winner = 0;
            $p1 = 0;
            $p2 = 0;

            $sets = [];

            while( $winner < 1 )
            {
                $set = [
                    'score1' => rand(1,12),
                    'score2' => rand(1,12),
                ];

                if( $set['score1'] > $set['score2'] )
                {
                    $p1++;
                }
                else
                {
                    $p2++;
                }

                $sets[] = $set;

                if( $p1 >= 2 )
                {
                    $winner = 1;
                    break;
                }
                if( $p2 >= 2 )
                {
                    $winner = 2;
                    break;
                }
            }


            $game = Game::create([
                'winner'   => $winner,
            ]);

            $players = [
                Player::find(rand(1,10)),
                Player::find(rand(1,10))
            ];

            foreach($players AS $player)
            {
                //print_r($player);
                $game->players()->attach( $player->id );
            }


            print_r(get_class($game->sets()));

            foreach($sets AS $set)
            {
                //$set = Set::create($set);

                //App\User::find(1)->roles()->save($role, ['expires' => $expires]);
                $game->sets()->create( $set );
            }
        }


    }
}