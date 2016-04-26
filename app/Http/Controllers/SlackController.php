<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as Controller;

use DB;
use App\Http\Requests;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

use Illuminate\Pagination\LengthAwarePaginator;
#use Illuminate\Contracts\Pagination\LengthAwarePaginator;

//Models
use App\Game;
use App\Player;

use App\AngryLadder\Slack;


class SlackController extends Controller
{
    protected $response;
    protected $slack;


    function __construct( )
    {
        $this->slack = new Slack();

    }

    function callback()
    {


        return 'callback';

    }

    function authorizeSlack( Request $request )
    {


        $this->slack->authorize( $request );





    }


}
