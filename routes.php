<?php

$router->get('/', 'HomeController@index');
$router->get('/listings', 'ListingController@index');
$router->get('/listings/create', 'ListingController@create', ['auth']);
$router->get('/listings/edit/{id}', 'ListingController@edit', ['auth']);
$router->get('/listings/{id}', 'ListingController@show');

$router->post('/listings', 'ListingController@store', ['auth']);
$router->put('/listings/{id}', 'ListingController@update', ['auth']);
$router->delete('/listings/{id}', 'ListingController@destroy', ['auth']);

// this part to view page
$router->get('/auth/register', 'UserController@create', ['guest']);
$router->get('/auth/login', 'UserController@login', ['guest']);
// for debug purpose
// $router->get('/auth/logout','UserController@logout');
// this part to controller 
$router->post('/auth/register','UserController@store', ['guest']);
$router->post('/auth/logout','UserController@logout', ['auth']);
$router->post('/auth/login','UserController@authenticate', ['guest']);

