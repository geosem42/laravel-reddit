<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Unauthenticated api routes
Route::group(['prefix' => '', 'middleware' => 'throttle:50,5'], function () {
    Route::post('/login', 'api\Auth\LoginController@login');

    Route::get('/media/delete/{key}', 'api\mediaUploadController@deleteFile');

    Route::post('/comments/load', 'api\commentsController@loadComments');

    Route::get('/subplebbits/search/{query}', 'api\searchSubPlebbitsController@search');
    Route::get('/users/search/{query}', 'api\searchUsersController@search');
});

// Authenticated api routes
Route::group(['prefix' => '', 'middleware' => ['throttle:50,5', 'auth:api']], function () {
    Route::post('/upload/media', 'api\mediaUploadController@upload');

    Route::post('/vote/{code}', 'api\votesController@vote');

    Route::post('/subscribe/{name}', 'api\subscriptionsApiController@subscribe');
    Route::post('/unsubscribe/{name}', 'api\subscriptionsApiController@unsubscribe');

    Route::post('/comments/add', 'api\commentsController@addComment');
    Route::post('/comments/load/auth', 'api\commentsController@loadComments');

    Route::post('/thread/delete/{code}', 'api\moderationController@deleteThread');
    Route::post('/comment/delete/{id}', 'api\moderationController@deleteComment');
});
