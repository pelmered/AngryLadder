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
//use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\Validator;


use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;



use Laravel\Lumen\Routing\Controller as BaseController;

abstract class ApiController extends Controller
{

    function __construct()
    {

    }


    protected $statusCode = 200;
    protected $errorCode = '';
    protected $errorDetails = '';

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


    function validate( Request $request, array $rules, array $messages = [], array $customAttributes = [] )
    {
        $validator = $this->getValidationFactory()->make($request->all(), $rules, $messages, $customAttributes);

        if ($validator->fails()) {

            $errors = $this->formatValidationErrors($validator);

            return $this->setStatusCode(400)
                ->setErrorCode('VALIDATION_ERROR')
                ->setErrorDetails($errors['detail'])
                ->respondWithError($errors['title']);
        }

        return true;
    }

    protected function formatValidationErrors(Validator $validator)
    {
        $errors = $validator->errors()->getMessages();

        $fields = 'Validation failed for: '.implode(', ', array_keys($errors));

        $errorString = '';

        foreach( $errors AS $field => $error )
        {
            $errorString .= ucfirst($field).': '.implode(', ', $error).' ';
        }

        return [
            'title'     => $fields,
            'detail'    => $errorString
        ];
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

    public function getErrorCode()
    {
        if( empty( $this->errorCode ) )
        {
            return 'GENERAL_ERROR';
        }
        return $this->errorCode;
    }
    /**
     * @param mixed $errorsCode
     */
    public function setErrorCode($errorsCode)
    {
        $this->errorCode = $errorsCode;

        return $this;
    }


    function getErrorDetails()
    {
        return $this->errorDetails;
    }

    function setErrorDetails($errorDetails)
    {
        $this->errorDetails = $errorDetails;

        return $this;
    }

    function respondNotFound( $message = 'Not found' )
    {
        return $this->setStatusCode(404)->setErrorCode('NOT_FOUND_ERROR')->respondWithError( $message );
    }
    function respondInternalError( $message = 'Internal Error' )
    {
        return $this->setStatusCode(500)->setErrorCode('INTERNAL_ERROR')->respondWithError( $message );
    }
    function respondNotImplemented( $message = 'Planned feature, but not implemented yet. ' )
    {
        return $this->setStatusCode(501)->setErrorCode('NOT_IMPLEMENTED_ERROR')->respondWithError( $message );
    }

    function respondWithError( $message )
    {

        return $this->respond([
            'status'    => 'error',
            'errors'     => [
                'code'      => $this->getErrorCode(),
                'status'    => $this->getStatusCode(),
                'title'     => $message,
                'detail'    => $this->getErrorDetails()
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
        if( !isset($data['status'] ) )
        {
            $data = ['status' => 'ok'] + $data;
        }

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


