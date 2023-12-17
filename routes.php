<?php

$router->get('/', 'HomeController@index');
$router->get('/listings', 'ListingController@index');
$router->get('/listings/create', 'ListingController@create');
$router->get('/listings/edit/{id}', 'ListingController@edit');
$router->get('/listings/{id}', 'ListingController@show');

$router->post('/listings', 'ListingController@store');
$router->put('/listings/{id}', 'ListingController@update');
$router->delete('/listings/{id}', 'ListingController@destroy');

// this part to view page
$router->get('/auth/register', 'UserController@create');
$router->get('/auth/login', 'UserController@login');
// for debug purpose
// $router->get('/auth/logout','UserController@logout');
// this part to controller 
$router->post('/auth/register','UserController@store');
$router->post('/auth/logout','UserController@logout');
$router->post('/auth/login','UserController@authenticate');

