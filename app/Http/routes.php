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

$app->group(['prefix' => 'api'], function() use ($app) {

    $app->POST('/auth/login', 'AuthController@postLogin');
    $app->POST('/auth/register', 'AuthController@register');
    $app->GET('/auth/user', 'AuthController@getUser');
    $app->PATCH('/auth/refresh', 'AuthController@patchRefresh');
    $app->DELETE('/auth/invalidate', 'AuthController@deleteInvalidate');


    $app->get('/categories', 'CategoryController@index');


    // -- Admin Actions
    $app->group(['prefix' => 'admin'], function () use ($app) {

        $app->post('/category/add', 'CategoryController@create');
        $app->post('/category/update', 'CategoryController@update');


    });

});


