<?php

/** @var \Laravel\Lumen\Routing\Router $router */

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
    return $router->app->version();
});

$router->delete('/emptyDatabase', 'UtilsController@empty');

$router->group(['prefix' => 'user', 'middleware' => 'jwt.auth'], function() use ($router){
    $router->get('me', 'UserController@me');
    $router->group(['prefix' => 'vehicles'], function() use ($router){
        $router->get('list', 'VehicleController@list');
        $router->get('vehicle/{vehicleID}', 'VehicleController@getByID');
        $router->post('add', 'VehicleController@insert');
        $router->delete('remove', 'VehicleController@remove');
    });
    $router->group(['prefix' => 'stays'], function() use ($router){
        $router->get('lasts', 'StayController@lasts');
    });
    $router->group(['prefix' => 'paymentMethods'], function() use ($router){
        $router->get('list', 'PaymentMethodsController@list');
        $router->post('add', 'PaymentMethodsController@add');
        $router->post('setDefault', 'PaymentMethodsController@setDefault');
        $router->delete('delete/{cardID}', 'PaymentMethodsController@delete');
    });
    $router->get('invoices', 'UserController@invoices');
});

$router->group(['prefix' => 'park', 'middleware' => 'jwt.auth'], function() use ($router){
    $router->get('status', 'PlaceController@status');
    $router->get('getFree', 'PlaceController@getFree');
});

$router->group(['prefix' => 'auth'], function () use ($router) {
    $router->post('login', 'AuthController@login');
    $router->post('refresh', 'AuthController@refresh');
    $router->post('register', 'AuthController@create');
    $router->group(['middleware' => 'jwt.auth'] , function () use ($router){
        $router->post('logout', 'AuthController@logout');
        $router->get('me', 'AuthController@me');
    });
});




