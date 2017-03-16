<?php namespace App\Transformers;

use App\PlayerRating;
use App\PlayerStats;
use League\Fractal\Resource\Collection;
use League\Fractal\TransformerAbstract;

use Carbon\Carbon;

class PlayerRankingTransformer extends TransformerAbstract {

    public function transform( $playerRating)
    {
        return $playerRating;
    }

}
