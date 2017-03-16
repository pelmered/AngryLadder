<?php

namespace App\Http\Controllers;

use pelmered\APIHelper\Controllers\ApiController;

use DB;
use Illuminate\Http\Request;
use App\Http\Requests;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Gate;

use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;

//Transformers
use App\Transformers\PlayerTransformer;

//Models
use App\Match;
use App\Player;

class StatsController extends ApiController
{
    protected $response;

    const RESOURCE_MODEL = 'App\Player';
    const RESOURCE_NAME = 'Player';

    protected $validationRules = [
        'update' => [
            'title' => 'required',
            'text'  => 'required'
        ],
        'store' => [
            'title'     => 'required',
            'text'      => 'required',
            'venue_id'  => 'exists:venues,id',
        ]
    ];

    function __construct( )
    {
        parent::__construct();
    }

    public function index(  )
    {
        //$collection->getData()

        return $this->notImplementedResponse();

        return $this->response( [
            'meta'  => [
                'list'  => 'stats',
                'type'  => 'overall'
            ],
            'data' => ['Not implemented yet']
        ] );
    }

    public function top( $type = 'toprated', $ladder = 'all_time' )
    {

        $page = $this->getCurrentPage();
        $limit = $this->getQueryLimit();

        $offset = ($page * $limit) - $limit;

        /*
        $players = Player::select( DB::raw('players.*, count(game_player.player_id) AS gamecount') )
            //$players = Player::select( DB::raw('players.*') )

            ->join('game_player', function($join)
            {
                $join->on('game_player.player_id', '=', 'players.id');
            })
            ->where('rating', '!=', 1000)
            ->groupBy('players.id')
            ->skip($offset)->take($limit);
        */

        /*
SELECT p.*, count(m.id) AS gamecount, pr.rating
FROM players p
JOIN matches m ON m.player1_id = p.id OR m.player2_id = p.id
JOIN player_ratings pr ON p.id = pr.player_id AND pr.ladder = 'all_time'
WHERE pr.rating != 1000
GROUP BY p.id, pr.id
         */

        DB::enableQueryLog();

        $players = Player::select( DB::raw('players.*, count(matches.id) AS gamecount, player_ratings.rating') )
            //$players = Player::select( DB::raw('players.*') )

            ->join('matches', function($join)
            {
                $join->on('matches.player1_id', '=', 'players.id');
                //$join->on('matches.player2_id', '=', 'players.id');
            })
            ->join('player_ratings', function($join)
            {
                $join->on('player_ratings.player_id', '=', 'players.id');
                //$join->on('player_ratings.ladder', '=', 'all_time');
            })
            ->where('rating', '!=', 1000)
            ->where('player_ratings.ladder', '!=', $ladder)
            ->groupBy('players.id', 'player_ratings.id')
            //->orderBy('gamecount', 'DESC')
            //->get();
            //->orderBy('gamecount', 'desc')
        ;

        switch( $type )
        {
            case 'mostgames':

                $players->orderBy('gamecount', 'desc');

                break;
            case 'toprated':
            default:

                $players->orderBy('player_ratings.rating', 'desc');

                break;
        }

        $players->skip($offset)->take($limit);

        $paginator = collect($players);
        $pagedData = $paginator->slice($offset, $limit)->all();
        $paginator = new \Illuminate\Pagination\LengthAwarePaginator($pagedData, count($paginator), $limit, $page);

        $collection = new Collection( $players->get(), new PlayerTransformer(), false);

        //dd(DB::getQueryLog());


        return $this->paginatedResponse( $paginator, [
            'meta'  => [
                'list'  => 'toplist',
                'type'  => $type
            ],
            'data' => $collection->getData()
        ]);
    }
}

