<?php namespace App\Transformers;

use App\PlayerStats;
use League\Fractal\Resource\Collection;
use League\Fractal\TransformerAbstract;

use Carbon\Carbon;

class PlayerStatsTransformer extends TransformerAbstract {

    public function transform(PlayerStats $playerStats)
    {

        $stats = $playerStats->toArray();

        //$stats['updated'] = $stats['updated'];
        $stats['updated'] = new Carbon(date("Y-m-d H:i:s", $stats['updated']));

        return $stats;
    }

}
