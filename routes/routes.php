<?php

use Core\Route;

$route = new Route;

$route->get('/', 'HomeController@index', 'auth');
$route->get('/login', 'LoginController@showlogin', 'guest');
$route->post('/login', 'LoginController@login', 'guest');
$route->get('/logout', 'LoginController@logout', 'auth');

//Demo Codes
$route->get('/select', 'HomeController@select', 'auth');
$route->get('/select2', 'HomeController@select2', 'auth');
$route->get('/insert', 'HomeController@insert', 'auth');
$route->get('/update', 'HomeController@update', 'auth');
$route->get('/requestValidation', 'HomeController@requestValidation', 'auth');
