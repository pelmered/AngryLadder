<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
/*
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
*/
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Input;


use Laravel\Lumen\Routing\Controller as BaseController;

abstract class ApiController extends Controller
{


    protected $statusCode = 200;

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
        return response()->json( $data, $this->getStatusCode(), $headers );
    }

    function respondWithPagination( LengthAwarePaginator $paginator, $data, $headers = [] )
    {
        $currentPage = Input::get('page');

        if( empty($data) || $currentPage > ceil( $paginator->total() / $paginator->perPage() ) || $currentPage < 0 )
        {
            $this->setStatusCode(404);
        }

        $data = array_merge( $data, [
            'pagination' => [
                'total_count'   => (int) $paginator->total(),
                'total_pages'   => (int) ceil( $paginator->total() / $paginator->perPage() ),
                'current_page'  => (int) $currentPage,
                'limit'         => (int) $paginator->perPage(),

                'prev_link'     => $paginator->previousPageUrl(),
                'next_link'     => $paginator->nextPageUrl()
            ]
        ]);

        return $this->respond( $data );
    }




}


