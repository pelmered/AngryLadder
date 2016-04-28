<?php namespace App\Transformers;

use App\Rank;
use League\Fractal\Resource\Collection;
use League\Fractal\TransformerAbstract;

class RankTransformer extends TransformerAbstract {

    public function transform(Rank $rank)
    {
        return [
            'weekly'    => $rank->weekly,
            'all_time'   => $rank->allTime,
        ];
    }

}
