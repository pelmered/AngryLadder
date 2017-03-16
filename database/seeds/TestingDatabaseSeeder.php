<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class TestingDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $this->call(LaddersTableSeeder::class);
        $this->call(PlayersTableSeeder::class);
        $this->call(MatchsTableSeeder::class);

        Model::reguard();
    }
}
