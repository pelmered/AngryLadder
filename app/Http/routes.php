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
    return view('index');
});

$app->group([
        'prefix' => 'v1/games',
        'namespace' => 'App\Http\Controllers',
        'middleware' => 'throttle:30000'
    ], function() use ($app) {

    $app->get('/', ['uses' => 'GamesController@index']);

    $app->get('/{id}', 'GamesController@show');

    $app->post('/', 'GamesController@store');

    $app->put('/{id}', 'GamesController@update');
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

    $app->post('/', 'PlayersController@store');

    $app->put('/{id}', 'PlayersController@update');
});

$app->group([
    'prefix' => 'slack',
    'namespace' => 'App\Http\Controllers'
], function() use ($app) {

    $app->get('/authorize', ['uses' => 'SlackController@authorizeSlack']);
    $app->get('/callback', ['uses' => 'SlackController@callback']);
});

