<?php

namespace App\Http\Controllers;

//use DB;
use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\Input;

use Illuminate\Support\Facades\DB;
#use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

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
        $game = Game::with('player1', 'player2')->find( $id );

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
        $limit = Input::get('limit') ?: 5;

        if( $limit > 50 )
        {
            $limit = 10;
        }
        $games = Game::with('player1','player2')->paginate($limit);

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

        $elo = new Elo( );
        $new_rankings = $elo->calculateGame( $game );

        $player1 = Player::find( $game->player1 );
        $player2 = Player::find( $game->player2 );

        $player1->adjustRating ( $new_rankings['player1'] );
        $player2->adjustRating ( $new_rankings['player2'] );


        $data = ['id' => $game->id] + $data;
        $data['ranking1'] = $new_rankings['player1'];
        $data['ranking2'] = $new_rankings['player2'];

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
