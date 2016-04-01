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

    }

    function respondNotFound( $msg = '' )
    {
        if( empty($msg) )
        {
            return parent::respondNotFound( 'Plugin not found' );
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
        $player = Player::find( $id );

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
        $limit = Input::get('limit') ?: 5;

        if( $limit > 50 )
        {
            $limit = 10;
        }
        $players = Player::paginate($limit);

        return $this->respondWithPagination( $players, [
            'data' => $players->all()
        ]);
    }

    public function top( $type = 'toprated' )
    {
        $page   = (int) Input::get('page') ?: 1;
        $limit  = (int) Input::get('limit') ?: 10;

        // Default
        if( empty($limit) || $limit == 0 )
        {
            $limit = 10;
        }
        // Max
        elseif( $limit > 100 )
        {
            $limit = 100;
        }

        $offset = ($page * $limit) - $limit;

        $players = Player::select( DB::raw('players.*, count(games.id) AS gamecount') )
            ->join('games', function($join)
            {
                $join->on('games.player1', '=', 'players.id');
                $join->orOn('games.player2', '=', 'players.id');
            })
            ->where('rating', '!=', 1000)
            ->groupBy('players.id');



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
        $Paginator = collect($players);
        $pagedData = $Paginator->slice($offset, $limit)->all();
        $Paginator = new \Illuminate\Pagination\LengthAwarePaginator($pagedData, count($Paginator), $limit, $page);


        return $this->respondWithPagination( $Paginator, [
            'meta'  => [
                'type'      => 'toplist',
                'type'  => $type
            ],
            'data' => $players->all()
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
            'name'      => $request->name,
            'slack_id'      => $request->name,
            'slack_name'      => $request->name,
            'rating'   => 1000
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        //
    }
}
