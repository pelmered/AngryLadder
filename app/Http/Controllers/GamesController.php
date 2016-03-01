<?php

namespace App\Http\Controllers;

//use DB;
use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\Input;

use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;

//Models
use App\Game;
use App\Player;

use App\AngryLadder\Elo;


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
        $game = Game::with('player1')->with('player2')->find( $id );

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
        $games = Game::with('player1')->with('player2')->get();

/*
        $games = DB::table('games')
            ->leftJoin('player', 'games.player1', '=', 'players.id')
            ->leftJoin('player2', 'games.player2', '=', 'players.id')
            ->get();
*/
        return $this->respond([
            'data' => $games->all()
        ]);



        $limit = 10;


        $limit = Input::get('limit') ?: 10;

        if( $limit > 100 )
        {
            $limit = 100;
        }
        $games = Game::with('contributor','tag')->paginate($limit);

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
        $data = [
            'player1'   => $request->player1,
            'player2'   => $request->player2,
            'score1'    => $request->score1,
            'score2'    => $request->score2
        ];

        $game = Game::create($data);

        /*
        $elo = new Elo( );
        $elo->calculateGame( $game );
        */

        $data['ranking1'] = 16;
        $data['ranking2'] = -12;

        return $this->respond([
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
