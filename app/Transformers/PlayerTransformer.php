<?php namespace App\Transformers;

use App\Player;
use League\Fractal\Resource\Collection;
use League\Fractal\TransformerAbstract;


use League\Fractal\Manager;
use League\Fractal\Resource\Item;

use League\Fractal\Serializer\ArraySerializer;


class PlayerTransformer extends TransformerAbstract {

    protected $defaultIncludes = [
        'rankings'
        //'ratings'
    ];

    public function transform(Player $player)
    {
        return [
            'name' => $player->name,
            'email' => $player->email,
            'avatar_url' => $player->avatar_url
        ];
    }


    public function includeRankings(Player $player)
    {

        $ranks = Player::getRanks($player->id)->toArray();

        $ratings = $player->getRatings();

        $data = [];


        foreach($ratings as $rating)
        {
            $data[$rating->ladder] = [
                'rating'    => $rating->rating,
                'ranking'   => $ranks[$rating->ladder]

            ];
        }


        return $this->item($data, new PlayerRankingTransformer(), false);


        return $this->collection($ratings, new PlayerRatingTransformer(), false);

        var_dump($data);


        return $data;

        var_dump($ratings);
        var_dump($ranks);


        die();

        foreach( $ranks as $ladder_id => $rank ) {

            $data[$ladder_id] = [

            ];
        }







        //$fractal->setSerializer(new ArraySerializer());
        //dd($this->collection($sets, new SetTransformer(), 'test'));
        //return $this->collection($sets, new SetTransformer());
        return $this->collection($ratings, new PlayerRatingTransformer(), false);
    }

    public function includeRatings(Player $player)
    {
        $ratings = $player->getRatings();

        //$fractal->setSerializer(new ArraySerializer());
        //dd($this->collection($sets, new SetTransformer(), 'test'));
        //return $this->collection($sets, new SetTransformer());
        return $this->collection($ratings, new PlayerRatingTransformer(), false);
    }


    public function includeSets(Player $player)
    {
        $sets = $player->sets;

        //var_dump($sets);

        //$fractal->setSerializer(new ArraySerializer());
        //dd($this->collection($sets, new SetTransformer(), 'test'));
        //return $this->collection($sets, new SetTransformer());
        return $this->collection($sets, new SetTransformer(), false);
    }



}
