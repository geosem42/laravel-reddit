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

/*For New Register Button*/

Route::get('/externalsignup', 'api\UsersController@externalsignup');
Route::any('externalauth','api\UsersController@externalauth');

Route::get('login/irt', 'Auth\LoginController@redirectToProvider');
Route::get('/login/irt/callback', 'Auth\LoginController@handleProviderCallback');

Route::get('register/irt', 'Auth\RegisterController@redirectToProvider');

Route::get('/', 'HomeController@index')->name('home');
Route::get('/s/{sort}', 'HomeController@index')->name('home');
Route::get('/g/{sort}', 'HomeController@index')->name('home');

Auth::routes();

Route::get('/activate', 'Auth\ActivationController@activate')->name('auth.activate');
Route::get('/activate/resend', 'Auth\ActivationController@showResendForm')->name('auth.activate.resend');
Route::post('/activate/resend', 'Auth\ActivationController@resend');




Route::get('/sublolhows/create', 'ManageSubLolhowsController@getNewSubLolhow')->name('sublolhow.create');
Route::post('/sublolhows/create', 'ManageSubLolhowsController@postNewSubLolhow');

Route::get('/submit', 'createThreadController@getCreateThread');
Route::post('/submit', 'createThreadController@postCreateThread');
Route::get('/p/{name}/submit', 'createThreadController@getCreateThread');
Route::post('/p/{name}/submit', 'createThreadController@postCreateThread');

Route::get('/p/{name}/edit', 'ManageSubLolhowsController@getEditLolhow');
Route::post('/p/{name}/edit', 'ManageSubLolhowsController@postEditLolhow');

Route::group(['prefix' => '', 'middleware' => 'throttle:30,5'], function () {
    Route::get('/p/{name}/edit/css', 'ManageSubLolhowsController@getEditLolhowCss');
    Route::post('/p/{name}/edit/css', 'ManageSubLolhowsController@postEditLolhowCss');
    Route::get('/cdn/css/{name}.css', 'ManageSubLolhowsController@loadcss');
});

Route::resource('bet', 'BetController');

Route::get('/p/{name}', 'subLolhowsController@subLolhow');
Route::get('/p/{name}/{sort}', 'subLolhowsController@subLolhow');

Route::get('/p/{name}/comments/{code}', 'commentsController@index');
Route::get('/p/{name}/comments/{code}/{title}', 'commentsController@index');

Route::get('/amp/p/{name}/comments/{code}/{title}', 'commentsController@index');
Route::get('/amp/p/{name}/comments/{code}', 'commentsController@index');

Route::get('/u/{name}', 'userProfileController@index');
Route::get('/u/{name}/{sort}', 'userProfileController@index');
Route::post('/updatekarma', 'userProfileController@updatekarma')->name('updatekarma');
Route::post('/submitbet', 'BetController@submitbet')->name('submitbet');

Route::get('/search', 'SearchController@search');
Route::get('/search/{sublolhow}', 'SearchController@search');

Route::get('/messages', 'MessagesController@inbox')->name('messages.inbox');
Route::get('/messages/send', 'MessagesController@GetSendMessage')->name('messages.send');
Route::get('/messages/send/{username}', 'MessagesController@GetSendMessage');
Route::post('/messages/send', 'MessagesController@PostSendMessage');
Route::get('/messages/view/{code}', 'MessagesController@ViewMessage')->name('message.view');
Route::post('/messages/view/{code}', 'MessagesController@ReplyMessage');
Route::post('/messages/markread', 'MessagesController@MarkAllRead')->name('messages.mark_read');

Route::get('/alerts/{code}', 'AlertsController@index');
