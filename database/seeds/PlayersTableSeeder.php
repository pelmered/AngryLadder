<?php

use Illuminate\Database\Seeder;
use Faker\Factory AS Faker;

class PlayersTableSeeder extends Seeder {

    
    public function run(  )
    {
        $faker = Faker::create();

        $slack_names = ['peter', 'grod', 'viktor', 'cluez' ]

        foreach( range(1, 10 ) as $index )
        {
            DB::table('players')->insert([
                'name'          => $faker->name,
                'email'         => $faker->email,
                'avatar_url'    => $faker->imageUrl(200, 200, 'cats'),
                'slack_id'      => '',
                'slack_name'    => $slack_names[rand(0,)],
                'rating'        => 1000
            ]);
        }


    }
}