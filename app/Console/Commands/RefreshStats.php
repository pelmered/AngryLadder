<?php namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Player;
use App\Jobs\RefreshPlayerStats;
use Cache;
use Illuminate\Support\Facades\Queue;

class RefreshStats extends Command {

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'stats:refresh';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stats:refresh';

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
        $players = Player::get();

        $playerCount = 0;
        $playerFailedCount = 0;

        foreach( $players AS $player )
        {
            $queueId = Queue::push(new RefreshPlayerStats( $player ));

            if( $queueId )
            {
                $this->info('Job queued for player #'.$player->id);
                $playerCount++;
            }
            else
            {
                $this->error('Error while queuing stat refresh for player #'.$player->id);
                $playerFailedCount++;
            }
        }

        $this->info('done!');
        $this->info('Added: '. $playerCount);
        $this->info('Failed: '. $playerFailedCount);
    }


}
