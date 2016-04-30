<?php namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Player;
use Cache;

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
        $ttl = 86400;

        $players = Player::get();

        foreach( $players AS $player )
        {
            $stats = $player::getStats($player->id, false);

            $attributes = $stats->getAttributes();

            print_r($attributes);

            foreach( $attributes AS $stat_key => $stat_value )
            {
                Cache::put('stats_player_'.$player->id.'_'.$stat_key, $stat_value, $ttl);
            }

            Cache::put('stats_player_'.$player->id.'_'.'updated', time(), $ttl);

            foreach( $attributes AS $stat_key => $stat_value )
            {
                $this->info( Cache::get('stats_player_'.$player->id.'_'.$stat_key));
            }

            $this->info('Cache saved for player #'.$player->id);
        }

        $this->info('done!');
    }


}
