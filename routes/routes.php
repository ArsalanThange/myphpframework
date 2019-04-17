<?php

use Core\Route;

$route = new Route;

$route->get('/', 'HomeController@index')->middleware('auth');
$route->get('/login', 'LoginController@showlogin')->middleware('guest');
$route->post('/login', 'LoginController@login')->middleware('guest');
$route->get('/logout', 'LoginController@logout', 'auth');

//Demo Codes
$route->get('/select', 'HomeController@select')->middleware('auth');
$route->get('/select2', 'HomeController@select2')->middleware('auth');
$route->get('/insert', 'HomeController@insert')->middleware('auth');
$route->get('/update', 'HomeController@update')->middleware('auth');
$route->get('/requestValidation', 'HomeController@requestValidation')->middleware('auth');
