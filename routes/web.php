<?php


Route::get('/channels', 'ChannelController@index');

Route::group(['prefix' => 'api'], function () {
    Route::get('{channel}/threads', 'ThreadController@index');
    Route::get('threads', 'ThreadController@index');
    // TODO: figure our why this has to be on the second spot
    Route::post('{channel}/threads', 'ThreadController@store')->middleware('jwt.auth');
    Route::delete('{channel}/threads/{thread}', 'ThreadController@destroy')->middleware('jwt.auth');
    Route::get('threads/{thread}', 'ThreadController@show');
});

Route::group(['prefix' => 'api'], function () {
    Route::get('profile/{user}', 'ProfileController@show');
});

Route::group(['prefix' => 'api'], function () {
    Route::get('{channel}/threads/{thread}/replies', 'RepliesController@index');
    Route::post('threads/{thread}/replies', 'RepliesController@store')->middleware('jwt.auth');
    Route::delete('replies/{reply}', 'RepliesController@destroy')->middleware('jwt.auth');
    Route::patch('replies/{reply}', 'RepliesController@update')->middleware('jwt.auth');

    Route::post('{channel}/threads/{thread}/subscriptions', 'SubscriptionsController@store')->middleware('jwt.auth');

    Route::post('replies/{reply}/favorites', 'FavoritesController@store')->middleware('jwt.auth');
});

Route::group(['prefix' => 'api'], function () {
    Route::post('{channel}/threads/{thread}/subscriptions', 'SubscriptionsController@store')->middleware('jwt.auth');
    Route::post('replies/{reply}/favorites', 'FavoritesController@store')->middleware('jwt.auth');
});

Route::group(
    [
        'middleware' => 'jwt.auth',
        'prefix' => '/api'
    ],
    function () {
        Route::get('notifications', 'UserNotificationsController@index');
        Route::delete('notifications/{notification}', 'UserNotificationsController@destroy')->middleware('jwt.auth');;
    }
);

Route::post('login', 'Auth\APILoginController@login');
// Route::post('logout', 'Auth\LoginController@logout');

// Registration Routes...
Route::post('register', 'Auth\RegisterController@register');

// Password Reset Routes...
Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail');
Route::post('password/reset', 'Auth\ResetPasswordController@reset');
