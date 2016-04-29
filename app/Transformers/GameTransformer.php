<?php namespace App\Transformers;

use App\Game;
use League\Fractal\Resource\Collection;
use League\Fractal\TransformerAbstract;


use League\Fractal\Manager;
use League\Fractal\Resource\Item;

use League\Fractal\Serializer\ArraySerializer;


class GameTransformer extends TransformerAbstract {

    protected $defaultIncludes = [
        'sets', 'players'
    ];

    public function transform(Game $game)
    {
        return [
            'winner'       => $game->winner,
            'rating_adjustments' => [$game->rating_adjustment_player1,$game->rating_adjustment_player2],   //_player1'       => $game->rating_adjustment_player1,
            //'rating_adjustment_player2' => $game->rating_adjustment_player2,
            'created_at' => $game->created_at
        ];
    }



    public function includeSets(Game $game)
    {
        $sets = $game->sets;

        //var_dump($sets);

        //$fractal->setSerializer(new ArraySerializer());
        //dd($this->collection($sets, new SetTransformer(), 'test'));
        //return $this->collection($sets, new SetTransformer());
        return $this->collection($sets, new SetTransformer(), false);
    }


    public function includePlayers(Game $game)
    {
        $players = $game->players;

        return $this->collection($players, new PlayerTransformer(), false);
    }


}
