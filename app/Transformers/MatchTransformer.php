<?php namespace App\Transformers;

use App\Match;
use League\Fractal\Resource\Collection;
use League\Fractal\TransformerAbstract;


use League\Fractal\Manager;
use League\Fractal\Resource\Item;

use League\Fractal\Serializer\ArraySerializer;


class MatchTransformer extends TransformerAbstract {

    protected $defaultIncludes = [
        'sets', 'player1', 'player2'
    ];

    public function transform(Match $game)
    {
        return [
            'winner'       => $game->winner,
            'rating_adjustments' => [
                'player1' => $game->rating_adjustment_player1,
                'player2' => $game->rating_adjustment_player2
            ],
            'created_at' => $game->created_at
        ];
    }



    public function includeSets(Match $game)
    {
        $sets = $game->sets;

        //var_dump($sets);

        //$fractal->setSerializer(new ArraySerializer());
        //dd($this->collection($sets, new SetTransformer(), 'test'));
        //return $this->collection($sets, new SetTransformer());
        return $this->collection($sets, new SetTransformer(), false);
    }


    public function includePlayer1(Match $game)
    {
        $player = $game->player1;

        return $this->item($player, new PlayerTransformer(), false);
    }
    public function includePlayer2(Match $game)
    {
        $player = $game->player2;

        return $this->item($player, new PlayerTransformer(), false);
    }


}
