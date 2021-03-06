<?php

use Illuminate\Http\Request;

// TODO: move all to the api file
Route::group([], function () {
    Route::get('/channels', 'ChannelController@index');
});

Route::group([], function () {
    // TODO: only one should exist
    Route::get('{channel}/threads', 'ThreadController@index');
    Route::get('threads', 'ThreadController@index');
    // TODO: figure our why this has to be on the second spot
    Route::post('{channel}/threads', 'ThreadController@store')->middleware('jwt.auth');

    Route::put('threads/{thread}', 'ThreadController@update')->middleware('jwt.auth');
    Route::delete('threads/{thread}', 'ThreadController@destroy')->middleware('jwt.auth');
    Route::get('threads/{thread}', 'ThreadController@show');
});

Route::group([], function () {
    Route::get('profile/{user}', 'ProfileController@show');
});

Route::group([], function () {
    Route::get('threads/{thread}/replies', 'RepliesController@index');
    Route::post('threads/{thread}/replies', 'RepliesController@store')->middleware('jwt.auth');

    Route::put('replies/{reply}', 'RepliesController@update')->middleware('jwt.auth');
    Route::delete('replies/{reply}', 'RepliesController@destroy')->middleware('jwt.auth');
    Route::patch('replies/{reply}', 'RepliesController@update')->middleware('jwt.auth');


    Route::post('/threads/{thread}/subscriptions', 'SubscriptionsController@store')->middleware('jwt.auth');

    Route::post('replies/{reply}/favorites', 'FavoritesController@store')->middleware('jwt.auth');
});


Route::group(
    [
        'middleware' => 'jwt.auth',
    ],
    function () {
        Route::get('notifications', 'UserNotificationsController@index');
        Route::delete('notifications/{notification}', 'UserNotificationsController@destroy')->middleware('jwt.auth');;
    }
);

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});


Route::post('login', 'Auth\APILoginController@login');
// Route::post('logout', 'Auth\LoginController@logout');

// Registration Routes...
Route::post('register', 'Auth\RegisterController@register');

Route::get('me', 'Auth\UserController@me')->middleware('jwt.auth');

// Password Reset Routes...
Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail');
Route::post('password/reset', 'Auth\ResetPasswordController@reset');
