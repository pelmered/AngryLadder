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

class PlayersController extends ApiController
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

    /**
     * Display a listing of the resource.
     *
     * @return \pelmered\APIHelper\Traits\Response
     */

    /**
     * @param MatchTransformer $transformer
     * @return Response
     */
    public function index(PlayerTransformer $transformer)
    {
        return $this->getList($transformer);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show(PlayerTransformer $transformer, $id)
    {
        return $this->getSingle($transformer, $id);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store()
    {
        return $this->storeResource();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update($id)
    {
        return $this->updateResource($id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        return $this->destroyResource($id);
    }


    public function stats(  )
    {

    }

}

