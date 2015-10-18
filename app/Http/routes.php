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

Route::get('contact', 'ContactController@index');

Route::get('services', 'ServicesController@index');

//Route::get('blog', 'ArticlesController@index');
//Route::post('blog', 'ArticlesController@store');
//Route::get('blog/create', 'ArticlesController@create');
//Route::get('blog/{id}/edit', 'ArticlesController@edit');
//Route::get('blog/{id}', 'ArticlesController@show');

Route::resource('articles', 'ArticlesController');
//Route::post('articles/upload', 'ArticlesController@postUpload');

// dropzone upload
//Route::get('/', ['as' => 'upload', 'uses' => 'ImageController@getUpload']);
Route::post('upload', ['as' => 'upload-post', 'uses' =>'ArticlesController@postUpload']);
Route::post('upload/delete', ['as' => 'upload-remove', 'uses' =>'ArticlesController@deleteUpload']);

Route::controllers([
    'auth' => 'Auth\AuthController',
    'password' => 'Auth\PasswordController',
]);

// Password reset routes...
Route::get('password/reset/{token}', 'Auth\PasswordController@getReset');
Route::post('password/reset', 'Auth\PasswordController@postReset');

//Route::post('subreddit', 'SubredditController@store');
//Route::get('subreddit/create', 'SubredditController@create');

Route::resource('subreddit', 'SubredditController');
Route::resource('subreddit.moderators', 'ModeratorsController');

Route::post('search/{subreddit}', ['as' => 'search', 'uses' => 'SubredditController@search']);

/*Route::post('comments/{post}', ['as' => 'comment', 'uses' => 'PostsController@createComment']);
Route::post('comments/{comment}/child', ['as' => 'child-comment', 'uses' => 'PostsController@createChildComment']);*/

Route::get('mysubreddits', [
    'as' => 'mysubreddits',
    'uses' => 'SubredditController@mySubreddits'
]);

Route::resource('posts', 'PostsController');
Route::resource('votes', 'VotesController');
Route::resource('profile', 'ProfilesController');

/*Route::post('{post}/post_this_comment', ['as' => 'post_this_comment', 'uses' => 'PostsController@post_this_comment']);

Route::get('{reply_comment/{comment}', 'PostsController@reply_comment');
Route::post('per_page', 'PostsController@per_page');*/

Route::get('u/{name}', [
    'as' => 'profile_path',
    'uses' => 'ProfilesController@show'
]);

Route::get('data/subreddits/{query?}', 'PostsController@getSubreddits');
Route::get('data/users/{query?}', 'ModeratorsController@getUsers');

Validator::extend('alpha_spaces', function($attribute, $value)
{
    return preg_match('"^[A-Za-z][A-Za-z0-9]*$"', $value);
});

