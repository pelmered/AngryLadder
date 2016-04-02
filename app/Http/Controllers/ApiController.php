<?php
namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as Controller;

use Illuminate\Http\Request;

use App\Http\Requests;
/*
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
*/
#use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Input;


use Laravel\Lumen\Routing\Controller as BaseController;

abstract class ApiController extends Controller
{


    protected $statusCode = 200;

    protected function getQueryLimit()
    {
        $limit = (int) Input::get('limit') ?: 10;

        if( $limit > 50 || $limit == 0 )
        {
            $limit = 10;
        }

        return $limit;
    }

    protected function getCurrentPage()
    {
        $page = (int) Input::get('page') ?: 1;

        if( $page == 0 )
        {
            $page = 1;
        }

        return $page;
    }

    /**
     * @return mixed
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * @param mixed $statusCode
     */
    public function setStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;

        return $this;
    }


    function respondNotFound( $message = 'Not found' )
    {
        return $this->setStatusCode(404)->respondWithError( $message );
    }
    function respondInternalError( $message = 'Internal Error' )
    {
        return $this->setStatusCode(500)->respondWithError( $message );
    }
    function respondNotImplemented( $message = 'Planned feature, but not implemented yet. ' )
    {
        return $this->setStatusCode(501)->respondWithError( $message );
    }

    function respondWithError( $message )
    {

        return $this->respond([
            'error' => [
                'message'       => $message,
                'status_code'   => $this->getStatusCode()
            ]
        ]);

    }

    function respondWithPng( $data )
    {
        $headers = [
                /*
            'Pragma' => 'public',
            'Expires' => '0',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Cache-Control' => 'private',
            'Content-Disposition' => 'attachment filename="$filename.csv";',
            'Content-Transfer-Encoding' => 'binary',
            */
            'Content-type' => 'image/png'
        ];

        return response()->download( $data, 'banner.png', $headers );
    }

    function respond( $data, $headers = [] )
    {
        $headers['Access-Control-Allow-Origin'] = 'http://angryladder.dev';

        return response()->json( $data, $this->getStatusCode(), $headers );
    }

    function respondWithPagination( LengthAwarePaginator $paginator, $data, $headers = [] )
    {
        $currentPage = $this->getCurrentPage();
        $limit = $this->getQueryLimit();

        if( empty($data) || $currentPage > ceil( $paginator->total() / $paginator->perPage() ) || $currentPage < 0 )
        {
            $this->setStatusCode(404);
        }

        $limitStr = '';

        if( $limit )
        {
            $limitStr = '&limit='.$limit;
        }

        $data = array_merge( $data, [
            'pagination' => [
                'total_count'   => (int) $paginator->total(),
                'total_pages'   => (int) ceil( $paginator->total() / $paginator->perPage() ),
                'current_page'  => (int) $currentPage,
                'limit'         => (int) $paginator->perPage(),

                'prev_link'     => $paginator->previousPageUrl().$limitStr,
                'next_link'     => $paginator->nextPageUrl().$limitStr
            ]
        ]);

        return $this->respond( $data, $headers );
    }




}


