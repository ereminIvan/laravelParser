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

Route::post('/panel/parser/source', [
    'before'    => 'auth',
    'uses'      => 'Admin\SourceParserController@sourceAdd',
    'as'        => 'parser-source-add',
]);
Route::get('/panel/parser/source', [
    'before'    => 'auth',
    'uses'      => 'Admin\SourceParserController@sourceList',
    'as'        => 'parser-sources',
]);
Route::get('/panel/parser/news', [
    'before'    => 'auth',
    'uses'      => 'Admin\SourceParserController@newsList',
    'as'        => 'parser-news'
]);
Route::get('/panel/parser/news-archive', [
    'before'    => 'auth',
    'uses'      => 'Admin\SourceParserController@newsArchiveList',
    'as'        => 'parser-news-archive'
]);
Route::get('/panel/parser/news/{id}', [
    'before'    => 'auth',
    'as'        => 'parser-news-by-id',
    'uses'      => 'Admin\SourceParserController@news',
]);
Route::post('/panel/parser/news/toggle-archive', ['before' => 'auth',
    'uses' => 'Admin\SourceParserController@newsToggleArchive']);