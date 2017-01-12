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

$app->group(['prefix' => 'api'], function () use ($app) {

    // -- Not Authenticated User Services
    $app->POST('/auth/login', 'AuthController@postLogin');
    $app->POST('/auth/register', 'AuthController@register');


    // -- Authenticated User Services
    $app->group(['middleware' => 'auth'], function ($app) {
        $app->GET('/auth/user', 'AuthController@getUser');
        $app->PATCH('/auth/refresh', 'AuthController@patchRefresh');
        $app->DELETE('/auth/invalidate', 'AuthController@deleteInvalidate');
    });



    // -- Categories Services
    $app->get('/categories', 'CategoryController@index');

    // -- Product Services
    $app->get('/products', 'ProductController@index');

    // -- Branch Services
    $app->get('/branches', 'BranchController@index');


    // -- Admin Services
    $app->group(['prefix' => 'admin', 'middleware' => 'admin'], function () use ($app) {

        // -- Branch services
        $app->post('/branch/add', 'BranchController@create');
        $app->post('/branch/update', 'BranchController@update');
        $app->post('/branch/delete', 'BranchController@delete');


        // -- Category services
        $app->post('/category/add', 'CategoryController@create');
        $app->post('/category/update', 'CategoryController@update');
        $app->post('/category/delete', 'CategoryController@delete');


        // -- Product services
        $app->post('/product/add', 'ProductController@create');
        $app->post('/product/update', 'ProductController@update');
        $app->post('/product/delete', 'ProductController@delete');


    });

});


