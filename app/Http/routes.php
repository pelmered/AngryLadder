<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$app->get('/', function () use ($app) {
    return $app->version();
});


$app->get('example/{id}', 'ExampleController@show');

/*
$app->get('/v1/game', function () use ($app) {
    return 'lol';
});
*/

/*
// Plugin list, can optionally include some meta and the most important metrics
$app->get('/v1/games', function () use ($app) {
    return 'lol';
});
*/



$app->group(['prefix' => 'v1/games', 'namespace' => 'App\Http\Controllers', 'middleware' => 'throttle:30000'], function() use ($app) {
    $app->get('/', ['uses' => 'GamesController@index']);
    //$app->get('/{id}', 'GamesController@show');
});


/*
$app->get('/v1/games', [
    'middleware' => 'throttle:30',
    'uses' => 'GamesController@index'
]);
*/


//$app->get('v1/games/challenge', 'ExampleController@index');

$app->post('v1/games/', 'GamesController@create');


