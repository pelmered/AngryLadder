<?php

use Illuminate\Database\Seeder;
//use Illuminate\Database\Eloquent;

use App\Game;
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

        $tables = ['Game', 'Player'];

        Game::truncate();
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
