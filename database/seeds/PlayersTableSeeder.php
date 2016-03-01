<?php

use Illuminate\Database\Seeder;
use Faker\Factory AS Faker;

class PlayersTableSeeder extends Seeder {

    
    public function run(  )
    {

        $faker = Faker::create();

        foreach( range(1, 10 ) as $index )
        {
            DB::table('players')->insert([
                'name' => $faker->name,
            ]);
        }


    }
}