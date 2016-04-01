<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\Input;

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

        switch( $type )
        {
            case 'toprated':


                //$players = Plugin::orderBy('weighted_rating', 'desc')->get();
                $players = Player::whereNotNull('rating')
                    ->orderBy('rating', 'desc')
                    ->paginate($limit);


            case 'mostgames':

                //TODO
                $players = Player::whereNotNull('rating')
                    ->where('rating', '!=', 1000)
                    ->orderBy('rating', 'desc')
                    ->paginate($limit);

                break;

            default:



                break;

        }


        $limit = Input::get('limit') ?: 5;

        if( $limit > 50 )
        {
            $limit = 10;
        }
        $players = Player::paginate($limit);

        return $this->respondWithPagination( $players, [
            'data' => $players->all()
        ]);

        /*
        $query = array_map( function($value) {
            return (array) $value;
        }, $query );
        */

        if( empty($players) )
        {
            $this->setStatusCode(404);
        }


        return $this->respond([
            'data' => $players->all()
        ]);

        /*
        var_dump($players);
*/

        //var_dump((array) $players);

        return $this->respondWithPagination( $players, [
            'meta'  => [
                'type'      => 'toplist',
                'sub_type'  => $type
            ],
            'data'  => $this->pluginTransformer->transformCollection(
                $players->all(),
                'TopRated',
                [ 'rankOffset' => $offset + 1 ]
            )
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
        $player = Player::create([
            'name'      => $request->name,
            'ranking'   => 1000
        ]);

        return $this->respond([
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
