<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', 'HomeController@index');

Route::controllers([
	'auth' => 'Auth\AuthController',
	'password' => 'Auth\PasswordController',
]);

//Route::get('/panel/social-parser', ['before' => 'auth', 'uses' => 'Admin\SocialParserController@index']);
//Route::get('/panel/social-parser/source', ['before' => 'auth', 'uses' => 'Admin\SocialParserController@sourceList']);
Route::get('/panel/social-parser/source/add', ['before' => 'auth', 'uses' => 'Admin\SocialParserController@addSource']);
//Route::get('/panel/social-parser/scheduler', ['before' => 'auth', 'uses' => 'Admin\SocialParserController@sourceList']);
//Route::get('/panel/social-parser/results', ['before' => 'auth', 'uses' => 'Admin\SocialParserController@resultList']);
