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

$router->get('/', 'ImageController@index');

$router->group(['prefix' => 'v1'], function () use ($router) {
    $router->get('random', [
        'middleware' => 'auth:5',
        'uses' => 'ImageController@random'
    ]);

    $router->get('image/{image}', [
        'middleware' => 'auth:1',
        'uses' => 'ImageController@show'
    ]);

    $router->get('type/{type}', [
        'middleware' => 'auth:20',
        'uses' => 'ImageController@type'
    ]);
});
