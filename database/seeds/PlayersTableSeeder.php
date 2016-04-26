<?php

use Illuminate\Database\Seeder;
use Faker\Factory AS Faker;

use App\Player;

class PlayersTableSeeder extends Seeder {

    
    public function run(  )
    {
        $faker = Faker::create();

        foreach( range(1, 10 ) as $index )
        {
            $firstName = $faker->firstName();
            $lastName = $faker->lastName();

            //DB::table('players')->insert([
            $player = Player::create([
                'name'          => $firstName . ' ' . $lastName,
                'email'         => $faker->email,
                'avatar_url'    => $faker->imageUrl(200, 200, 'cats'),
                'slack_id'      => $faker->numberBetween(10000, 99999),
                'slack_name'    => strtolower($firstName),
                'rating'        => 1000,
                'added_from'    => 'seed'
            ]);
        }


    }
}