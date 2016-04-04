<?php

namespace App\Http\Controllers;

//use DB;
use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\Input;

use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

//Models
use App\Game;
use App\Player;

class GamesController extends ApiController
{

    protected $pluginTransformer;

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
        $game = Game::with('players', 'sets')->find( $id );

        if( !$game )
        {
            return $this->respondNotFound( );
        }

        return $this->respond([
            'data' => $game
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

        $games = Game::with('players', 'sets')
            ->orderBy('updated_at', 'desc')
            ->paginate($limit);

        return $this->respondWithPagination( $games, [
            'data' => $games->all()
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
        $this->validate($request, [
            'players' => 'required',
            'scores' => 'required',
        ]);

        $playerData = $request->players;
        $scoreData = $request->scores;

        if( count($playerData) != 2 )
        {
            return $this->respondWithError( 'Can only be two players in pingpong!' );
        }

        if( count($scoreData) != 3 )
        {
            return $this->respondWithError( 'Official ladder matches must have 3 sets!' );
        }

        $game = Game::createNewgame( $playerData, $scoreData );

        if( isset( $game['error'] ) )
        {
            return $this->respondWithError( $game['error'] );
        }

        $game = Game::with('players', 'sets')->find( $game->id );

        $data = $game->toArray();

        return $this->setStatusCode(201)->respond([
            'meta' => [
                'message' => 'Game created with ID: ' . $game->id
            ],
            'data' => $data
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
