<?php

use App\Image;

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

$router->get('/', function () use ($router) {
    $image = Image::inRandomOrder()->limit(1)->first();
    
    // Caches the total amount of images for 24 hours
    $total = app('cache')->remember('total', 60 * 24, function () {
        return Image::count();
    });

    return view('welcome', compact('image', 'total'));
});

$router->group(['prefix' => 'v1', 'middleware' => 'auth'], function () use ($router) {
    $router->get('random', 'ImageController@random');
    $router->get('image/{image}', 'ImageController@show');
    $router->get('type/{type}', 'ImageController@type');
});
