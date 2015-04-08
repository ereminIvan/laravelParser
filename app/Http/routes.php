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

Route::post('/panel/social-parser/source', ['before' => 'auth', 'uses' => 'Admin\SourceParserController@sourceAdd']);
Route::get('/panel/social-parser/source', ['before' => 'auth', 'uses' => 'Admin\SourceParserController@sourceList']);
Route::get('/panel/social-parser/news', ['before' => 'auth', 'uses' => 'Admin\SourceParserController@newsList']);
Route::get('/panel/social-parser/news/{id}', [
    'before'    => 'auth',
    'as'        => 'parser-news-by-id',
    'uses'      => 'Admin\SourceParserController@news']);