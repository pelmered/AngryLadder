<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent;
use Illuminate\Database\Eloquent\Model;

use App\Match;
use App\Player;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {


        //Eloquent::ungard();
        Model::unguard();

        $this->call(LaddersTableSeeder::class);
        $this->call(PlayersTableSeeder::class);
        $this->call(MatchesTableSeeder::class);

        Model::reguard();

        return;

        //Eloquent::ungard();

        $tables = ['Ladder','Player','Match'];

        Match::truncate();
        Player::truncate();

        foreach( $tables as $table )
        {
            //$table::truncate();

            require $table.'sTableSeeder.php';

            // $this->call('UserTableSeeder');
            $this->call($table.'sTableSeeder');
        }

    }
}
