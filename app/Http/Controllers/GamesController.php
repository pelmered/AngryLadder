<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\Input;

/*
use App\WPUnity\Transformers\PluginMetaTransformer;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;
*/

//Models
use App\Game;
use App\Player;

/*
// Transformers
use App\WPUnity\Transformers\PluginTransformer;
use App\WPUnity\Transformers\MetricTransformer;
use App\WPUnity\Transformers\TagTransformer;
use App\WPUnity\Transformers\TagMetaTransformer;
*/

//use Sorskod\Larasponse\Larasponse;

class GamesController extends ApiController
{

    protected $pluginTransformer;

    protected $response;


    //function __construct( PluginTransformer $pluginTransformer )
    function __construct( )
    {
        //$this->pluginTransformer = $pluginTransformer;
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

    /*
    public function __construct(Larasponse $response)
    {
        $this->response = $response;

        // The Fractal parseIncludes() is available to use here
        $this->response->parseIncludes(Input::get('includes'));
    }
    */

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $games = game::with('player1')->with('player2')->get();

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

        $games = Game::with('player')->paginate($limit);

        return $this->respond([
            'data' => $games->all()
        ]);

        return print_r($games, true);

        return;


        $limit = Input::get('limit') ?: 10;

        if( $limit > 100 )
        {
            $limit = 100;
        }
        $plugins = Game::with('contributor','tag')->paginate($limit);

        //$plugins_data = $this->pluginTransformer->transformCollection( $plugins->all() );


        /*
        foreach( $plugins->contributors AS $contributor )
        {
            $plugins_data['contributors'][] = $contributor->name;
        }
        */



        //print_r($plugins_data);
        //die();

        return $this->respondWithPagination( $plugins, [
            'data' => $this->pluginTransformer->transformCollection( $plugins->all() )
        ]);

    }

    public function toplist( $type = 'recent' )
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

                /*
                Bayesian Average
                http://blog.ekini.net/2013/08/18/getting-the-bayesian-average-for-rankings-mysql/
                */
                /*
                $avgs = DB::select('SELECT AVG(rating) AS avarage_rating, AVG(num_ratings) AS avarage_num_ratings
                    FROM plugins
                    WHERE num_ratings > 0');

                $query = (array) DB::select('SELECT plugins.*,
                    (('.$avgs[0]->avarage_num_ratings.' * '.$avgs[0]->avarage_rating.') +
                    (plugins.num_ratings * plugins.rating)) /
                    ('.$avgs[0]->avarage_num_ratings.' + plugins.num_ratings) as weighted_rating
                FROM plugins
                WHERE plugins.num_ratings > 0
                ORDER BY weighted_rating DESC
                LIMIT '.$offset.', '.$limit.'');
                */

                //$plugins = Plugin::orderBy('weighted_rating', 'desc')->get();
                $plugins = Plugin::whereNotNull('weighted_rating')
                    ->orderBy('weighted_rating', 'desc')
                    ->paginate($limit);

                break;
            case 'codequality' :

                $plugins = Plugin::whereNotNull('codequality')
                    //Join tags and metrics
                    ->orderBy('codequality', 'desc')
                    ->paginate($limit);

            default:



                break;

        }

        /*
        $query = array_map( function($value) {
            return (array) $value;
        }, $query );
        */

        if( empty($plugins) )
        {
            $this->setStatusCode(404);
        }

        /*
        var_dump($plugins);
*/

        //var_dump((array) $plugins);

        return $this->respondWithPagination( $plugins, [
            'meta'  => [
                'type'      => 'toplist',
                'sub_type'  => $type
            ],
            'data'  => $this->pluginTransformer->transformCollection(
                $plugins->all(),
                'TopRated',
                [ 'rankOffset' => $offset + 1 ]
            )
        ]);
    }




    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show( $slug )
    {
        $plugin = Plugin::where('slug', $slug )->first();
        //$plugin = Plugin::find( $id );

        if( !$plugin )
        {
            return $this->respondNotFound( );
        }

        return $this->respond([
            'data' => $this->pluginTransformer->transform( $plugin )
        ]);

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        //
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
