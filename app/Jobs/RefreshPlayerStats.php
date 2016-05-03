<?php

namespace App\Jobs;

use app\Player;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class RefreshPlayerStats extends Job
{
    private $player;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct( Player $player )
    {
        $this->player = $player;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $ttl = 86400;
        $player = $this->player;
        $stats = $player::getStats($player->id, false);

        $attributes = $stats->getAttributes();

        foreach( $attributes AS $stat_key => $stat_value )
        {
            Cache::put('stats_player_'.$player->id.'_'.$stat_key, $stat_value, $ttl);
        }

        Cache::put('stats_player_'.$player->id.'_'.'updated', time(), $ttl);

        Log::info('Cache saved for player #'.$player->id, $attributes);
    }
}
