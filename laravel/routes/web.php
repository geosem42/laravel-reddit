<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', 'HomeController@index')->name('home');
Route::get('/s/{sort}', 'HomeController@index')->name('home');
Route::get('/g/{sort}', 'HomeController@index')->name('home');

Auth::routes();

Route::get('/activate', 'Auth\ActivationController@activate')->name('auth.activate');
Route::get('/activate/resend', 'Auth\ActivationController@showResendForm')->name('auth.activate.resend');
Route::post('/activate/resend', 'Auth\ActivationController@resend');

Route::get('/subplebbits/create', 'ManageSubPlebbitsController@getNewSubPlebbit')->name('subplebbit.create');
Route::post('/subplebbits/create', 'ManageSubPlebbitsController@postNewSubPlebbit');

Route::get('/submit', 'createThreadController@getCreateThread');
Route::post('/submit', 'createThreadController@postCreateThread');
Route::get('/p/{name}/submit', 'createThreadController@getCreateThread');
Route::post('/p/{name}/submit', 'createThreadController@postCreateThread');

Route::get('/p/{name}/edit', 'ManageSubPlebbitsController@getEditPlebbit');
Route::post('/p/{name}/edit', 'ManageSubPlebbitsController@postEditPlebbit');

Route::group(['prefix' => '', 'middleware' => 'throttle:30,5'], function () {
    Route::get('/p/{name}/edit/css', 'ManageSubPlebbitsController@getEditPlebbitCss');
    Route::post('/p/{name}/edit/css', 'ManageSubPlebbitsController@postEditPlebbitCss');
    Route::get('/cdn/css/{name}.css', 'ManageSubPlebbitsController@loadcss');
});

Route::get('/p/{name}', 'subPlebbitsController@subPlebbit');
Route::get('/p/{name}/{sort}', 'subPlebbitsController@subPlebbit');

Route::get('/p/{name}/comments/{code}', 'commentsController@index');
Route::get('/p/{name}/comments/{code}/{title}', 'commentsController@index');

Route::get('/amp/p/{name}/comments/{code}/{title}', 'commentsController@index');
Route::get('/amp/p/{name}/comments/{code}', 'commentsController@index');

Route::get('/u/{name}', 'userProfileController@index');
Route::get('/u/{name}/{sort}', 'userProfileController@index');

Route::get('/search', 'SearchController@search');
Route::get('/search/{subplebbit}', 'SearchController@search');

Route::get('/messages', 'MessagesController@inbox')->name('messages.inbox');
Route::get('/messages/send', 'MessagesController@GetSendMessage')->name('messages.send');
Route::get('/messages/send/{username}', 'MessagesController@GetSendMessage');
Route::post('/messages/send', 'MessagesController@PostSendMessage');
Route::get('/messages/view/{code}', 'MessagesController@ViewMessage')->name('message.view');
Route::post('/messages/view/{code}', 'MessagesController@ReplyMessage');
Route::post('/messages/markread', 'MessagesController@MarkAllRead')->name('messages.mark_read');

Route::get('/alerts/{code}', 'AlertsController@index');