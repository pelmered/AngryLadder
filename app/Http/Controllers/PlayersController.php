<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\Input;

use Illuminate\Pagination\LengthAwarePaginator;
#use Illuminate\Contracts\Pagination\LengthAwarePaginator;

//Models
use App\Game;
use App\Player;

use App\AngryLadder\Elo;


class PlayersController extends ApiController
{
    protected $response;

    function __construct( )
    {
        parent::__construct();
    }

    function respondNotFound( $msg = '' )
    {
        if( empty($msg) )
        {
            return parent::respondNotFound( 'Player not found' );
        }
        else
        {
            return parent::respondNotFound( $msg );
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show( $id )
    {
        $player = Player::getByIDorSlackID( $id );

        if( !$player )
        {
            return $this->respondNotFound( );
        }

        return $this->respond([
            'data' => $player
        ]);
    }


    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $limit = $this->getQueryLimit();

        $players = Player::paginate($limit);

        return $this->respondWithPagination( $players, [
            'data' => $players->all()
        ]);
    }

    public function top( $type = 'toprated' )
    {
        $page = $this->getCurrentPage();
        $limit = $this->getQueryLimit();

        $offset = ($page * $limit) - $limit;

        $players = Player::select( DB::raw('players.*, count(games.id) AS gamecount') )
            ->join('games', function($join)
            {
                $join->on('games.player1', '=', 'players.id');
                $join->orOn('games.player2', '=', 'players.id');
            })
            ->where('rating', '!=', 1000)
            ->groupBy('players.id')
            ->skip($offset)->take($limit);

        switch( $type )
        {
            case 'toprated':

                $players->orderBy('rating', 'desc');

            case 'mostgames':

                $players->orderBy('gamecount', 'desc');

                break;

            default:

                break;
        }

        /**
         * If we use Query Builder with groupBy we need to create custom pagination
         * https://laravel.com/docs/5.1/pagination#basic-usage
         */
        $paginator = collect($players);
        $pagedData = $paginator->slice($offset, $limit)->all();
        $paginator = new \Illuminate\Pagination\LengthAwarePaginator($pagedData, count($paginator), $limit, $page);


        return $this->respondWithPagination( $paginator, [
            'meta'  => [
                'list'  => 'toplist',
                'type'  => $type
            ],
            'data' => $players->get()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request)
    {
        //TODO: Better validation

        if( strlen( $request->name ) < 5 )
        {
            return 'error';
        }

        $player = Player::create([
            'name'          => $request->name,
            'slack_id'      => $request->slack_id,
            'slack_name'    => $request->slack_name,
            'rating'        => 1000
        ]);

        return $this->setStatusCode(201)->respond([
            'meta' => [
                'message' => 'Player created with ID: ' . $player->id
            ],
            'data' => $player
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        return $this->respondNotImplemented();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        return $this->respondNotImplemented();
    }
}
