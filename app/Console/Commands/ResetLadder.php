<?php namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Player;
use App\Jobs\RefreshPlayerStats;
use Cache;
use Illuminate\Support\Facades\Queue;

class ResetLadder extends Command {

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'ladder:reset';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ladder:reset {type?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '.';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire()
    {


        $periods = config('ladder.ladders');

        print_r($periods);

        dd($periods);

        echo 'hej';

        return;


        dd();
        $players = Player::get();

        $playerCount = 0;

        foreach( $players AS $player )
        {
            $player->rating_weekly = 1000;
            $player->save();

            $playerCount++;
        }

        $this->info('done!');
        $this->info($playerCount . ' Players reset');
    }


}
