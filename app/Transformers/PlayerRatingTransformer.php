<?php namespace App\Transformers;

use App\PlayerRating;
use App\PlayerStats;
use League\Fractal\Resource\Collection;
use League\Fractal\TransformerAbstract;

use Carbon\Carbon;

class PlayerRatingTransformer extends TransformerAbstract {

    public function transform(PlayerRating $playerRating)
    {

        return [
            'ladder'                => $playerRating->ladder,
            'rating'                => $playerRating->rating,
            'rating_mu'             => $playerRating->rating_mu,
            'rating_deviation'      => $playerRating->rating_deviation,
            'rating_deviation_phi'  => $playerRating->rating_deviation_phi,
            'rating_volatility'     => $playerRating->rating_volatility,
            'updated_at'            => $playerRating->updated_at
        ];

        $ratings = $playerRating->toArray();

        //var_dump($ratings);

        //$ratings['updated'] = $ratings['updated'];
        //$ratings['updated'] = new Carbon(date("Y-m-d H:i:s", $ratings['updated']));

        return $ratings;
    }

}
