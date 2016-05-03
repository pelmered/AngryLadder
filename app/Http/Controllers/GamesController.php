<?php

namespace App\Http\Controllers;

//use DB;
use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\Input;

use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;


use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;

use League\Fractal\Serializer\ArraySerializer;

//Transformers
use App\Transformers\ApiSerializer;
use App\Transformers\GameTransformer;

//Models
use App\Game;
use App\Player;

class GamesController extends ApiController
{

    protected $pluginTransformer;

    protected $response;


    function __construct( )
    {
        parent::__construct();
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
    //public function show( $id )

    public function show(Manager $fractal, GameTransformer $gameTransformer, $gameId)
    {
        $fractal->setSerializer(new ApiSerializer());

        $game = game::find( $gameId );

        if( !$game )
        {
            return $this->respondNotFound( );
        }

        $item = new Item($game, $gameTransformer);

        $data = $fractal->createData($item)->toArray();

        return $this->respond($data);




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
    public function index(Manager $fractal, GameTransformer $gameTransformer)
    {
        $fractal->setSerializer(new ApiSerializer());


        $limit = $this->getQueryLimit();

        if (isset($_GET['player'])) {

            $playerId = $_GET['player'];

            $games = Player::getByIDorSlackID($playerId);

            if( !$games )
            {
                return $this->respondNotFound( );
            }

            $games = $games->games()->orderBy('updated_at', 'desc')->paginate($limit); //->paginate($limit);

        }
        else
        {
            $games = Game::orderBy('updated_at', 'desc')->paginate($limit);
        }

        $collection = new Collection($games, $gameTransformer);

        $data = $fractal->createData($collection)->toArray();


        return $this->respondWithPagination($games,$data);


    //public function index()
    //{
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

        $valid = $this->validate($request, [
            'players' => 'required',
            'sets' => 'required',
        ]);

        if( $valid !== true )
        {
            return $valid;
        }

        $playerData = $request->players;
        $setsData = $request->sets;

        if( count($playerData) != 2 )
        {
            return $this->respondWithError( 'Can only be two players in pingpong!' );
        }

        $setsCount = count($setsData);

        if( $setsCount != 2 && $setsCount != 3 )
        {
            return $this->respondWithError( 'Official ladder matches must have 2 or 3 sets!' );
        }

        $playerData = Player::getPlayersFromJSON($playerData, true);

        $game = Game::createNewGame( $playerData, $setsData );

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
