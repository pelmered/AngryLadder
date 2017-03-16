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
    return redirect('v1');
});
$app->get('/v1', function () use ($app) {
    return view('index');
});

$app->group([
        'prefix' => 'v1/matches',
        'namespace' => 'App\Http\Controllers',
        'middleware' => 'throttle:30000'
    ], function() use ($app) {

    $app->get('/', ['uses' => 'MatchesController@index']);

    $app->get('/{id}', 'MatchesController@show');

    $app->post('/', 'MatchesController@store');

    $app->put('/{id}', 'MatchesController@update');
});


$app->group([
    'prefix' => 'v1/players',
    'namespace' => 'App\Http\Controllers',
    'middleware' => 'throttle:30000'
], function() use ($app) {

    $app->get('/', ['uses' => 'PlayersController@index']);

    $app->get('/top', ['uses' => 'PlayersController@top']);
    $app->get('/top/{type}', ['uses' => 'PlayersController@top']);

    $app->get('/{id}', 'PlayersController@show');

    $app->get('/{id}/stats', 'PlayersController@stats');

    $app->post('/', 'PlayersController@store');

    $app->put('/{id}', 'PlayersController@update');
});

$app->group([
    'prefix' => 'v1',
    'namespace' => 'App\Http\Controllers',
    'middleware' => 'throttle:30000'
], function() use ($app) {

    $app->get('/stats', ['uses' => 'StatsController@index']);
    $app->get('/top', ['uses' => 'StatsController@top']);
    $app->get('/top/{type}', ['uses' => 'StatsController@top']);

});



$app->group([
    'prefix' => 'slack',
    'namespace' => 'App\Http\Controllers'
], function() use ($app) {

    $app->get('/authorize', ['uses' => 'SlackController@authorizeSlack']);
    $app->get('/callback', ['uses' => 'SlackController@callback']);
});

