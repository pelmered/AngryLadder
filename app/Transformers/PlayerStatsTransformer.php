<?php namespace App\Transformers;

use App\PlayerStats;
use League\Fractal\Resource\Collection;
use League\Fractal\TransformerAbstract;

class PlayerStatsTransformer extends TransformerAbstract {

    public function transform(PlayerStats $playerStats)
    {

        $stats = $playerStats->toArray();

        return $stats;
    }

}
