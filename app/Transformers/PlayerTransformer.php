<?php namespace App\Transformers;

use App\Player;
use App\Rank;
use App\PlayerStats;
use League\Fractal\Resource\Collection;
use League\Fractal\TransformerAbstract;

class PlayerTransformer extends TransformerAbstract {

    protected $defaultIncludes = [
        'rank'
        //'game'
    ];
    protected $availableIncludes = [
        'stats', 'games'
    ];

    public function transform(Player $player)
    {
        return [
            'name'          => $player->name,
            'slack_id'      => $player->slack_id,
            'slack_name'    => $player->slack_name,
            'email'         => $player->email,
            'avatar_url'    => $player->avatar_url,
            'ratings'       => [
                'weekly' => $player->rating_weekly,
                'all_time' => $player->rating
            ]
        ];
    }


    public function includeGames(Player $player)
    {
        $games = $player->games()->get();

        return $this->collection($games, new GameTransformer);
    }

    public function includeRank(Player $player)
    {
        $rank = $player::getRank($player->id);

        return $this->item($rank, new RankTransformer, false);



        return $this->item(new Rank($rank), new RankTransformer, false);


        $rankCollection = new \Illuminate\Database\Eloquent\Collection;

        $rankCollection->add(new Rank($rank));

        return $this->collection($rankCollection, new RankTransformer);
    }

    public function includeStats(Player $player)
    {
        $stats = $player::getStats($player->id);

        return $this->item($stats, new PlayerStatsTransformer, false);
    }

}
