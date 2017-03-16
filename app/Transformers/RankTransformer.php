<?php namespace App\Transformers;

use App\Rank;
use League\Fractal\Resource\Collection;
use League\Fractal\TransformerAbstract;

class RankTransformer extends TransformerAbstract {

    public function transform(Rank $rank)
    {

        $ladders = config('ladder.ladders');

        $data = [];

        foreach( $ladders as $ladder_id => $ladder ) {
            if( isset( $rank->$ladder_id ) ) {
                $data[$ladder_id] = $rank->$ladder_id;
            }
        }

        return $data;

        return [
            'weekly'    => $rank->weekly,
            'all_time'   => $rank->allTime,
        ];
    }

}
