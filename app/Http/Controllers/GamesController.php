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
        //$game = Game::with('player1', 'player2')->find( $id );
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


        return $this->setStatusCode(201)->respond([
            'meta' => [
                'message' => 'Game created with ID: ' . $game->id
            ],
            'data' => $game
        ]);

        print_r($game);

        return;

        $winner = 0;
        $p1 = 0;
        $p2 = 0;

        $sets = [];

        //while( $win == false )
        foreach( $scoreData AS $score )
        {
            $set = [
                'score1' => $score['set'][0],
                'score2' => $score['set'][1]
            ];

            if( $set['score1'] > $set['score2'] )
            {
                $p1++;
            }
            else
            {
                $p2++;
            }

            $sets[] = $set;

            if( $p1 >= 2 )
            {
                $winner = 1;
                break;
            }
            if( $p2 >= 2 )
            {
                $winner = 2;
                break;
            }
        }

        if( $winner < 0 )
        {
            return $this->respondWithError( 'No winner in game. Plz play moar!' );
        }


        $players = Player::getPlayersFromJSON($playerData);

        if( isset($players['error']) )
        {
            return $this->respondWithError( 'Error: Player could not be found: ' . print_r($players['error'], true) );
        }

        $p = 0;

        foreach($playerData AS $player)
        {

            if( is_array($player) )
            {
                $playerObj = $players[$p];
                foreach($player AS $key => $value)
                {
                    $playerObj->$key = $value;
                }
                $playerObj->save();
                $players[$p++] = $playerObj;
            }
        }

        $game = Game::create([
            'winner'    => $winner
        ]);

        print_r($game);




        // Add sets with relation to game
        print_r(get_class($game->players()));

        foreach($players AS $player)
        {
            //print_r($player);
            $game->players()->attach( $player->id );
        }


        // Add sets with relation to game
        print_r(get_class($game->sets()));

        foreach($sets AS $set)
        {
            print_r($set);
            //$set = Set::create($set);

            //App\User::find(1)->roles()->save($role, ['expires' => $expires]);
            $game->sets()->create( $set );
        }


        die();


        $game = Game::create($data);

        $elo = new Elo( );
        $new_rankings = $elo->calculateGame( $game );

        $player1 = Player::getByIDorSlackID( $game->player1 );
        $player2 = Player::getByIDorSlackID( $game->player2 );

        $player1->adjustRating ( $new_rankings['player1'] );
        $player2->adjustRating ( $new_rankings['player2'] );







        die();


        foreach($sets AS $set)
        {
            //$set = Set::create($set);

            //App\User::find(1)->roles()->save($role, ['expires' => $expires]);
            $game->sets()->create( $set );
        }

        foreach($players AS $_player)
        {
            if( is_numeric($_player) && intval($_player) > 0)
            {
                $player = Player::find( $_player );
            }
            else if( is_array($_player) )
            {
                $player = Player::getByIDorSlackID($_player);
            }

            if( empty($player) )
            {
                return $this->respondWithError( 'Error: Player could not be found: ' . $_player );
            }

            if( is_array($_player) )
            {
                foreach($_player AS $key => $value)
                {
                    $player->$key = $value;
                }

                $player->save();
            }

        }




        die();


        dd($request->players);



        die();




        //TODO: move to model
        $data = [
            'player1'   => $request->player1,
            'player2'   => $request->player2,
            'score1'    => $request->score1,
            'score2'    => $request->score2
        ];

        $game = Game::create($data);

        $elo = new Elo( );
        $new_rankings = $elo->calculateGame( $game );

        $player1 = Player::getByIDorSlackID( $game->player1 );
        $player2 = Player::getByIDorSlackID( $game->player2 );

        $player1->adjustRating ( $new_rankings['player1'] );
        $player2->adjustRating ( $new_rankings['player2'] );


        $data = ['id' => $game->id] + $data;
        $data['ranking1'] = $new_rankings['player1'];
        $data['ranking2'] = $new_rankings['player2'];


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
