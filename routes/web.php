<?php


Route::get('/channels', 'ChannelController@index');


Route::group(['prefix' => '/api'], function () {
    Route::get('/threads', 'ThreadController@index');
    Route::get('/threads/{channel}', 'ThreadController@index');
    Route::post('/threads/{channel}', 'ThreadController@store')->middleware('jwt.auth');
    Route::delete('/threads/{channel}/{thread}', 'ThreadController@destroy')->middleware('jwt.auth');
    Route::get('/threads/{channel}/{thread}', 'ThreadController@show');
});




Route::group(
    [
        'middleware' => 'jwt.auth',
        'prefix' => '/api'
    ],
    function () {

        Route::get('/threads/{channel}/{thread}/replies', 'RepliesController@index');
        Route::post('/threads/{thread}/replies', 'RepliesController@store')->middleware('auth');
        Route::delete('/replies/{reply}', 'RepliesController@destroy')->middleware('auth');
        Route::patch('/replies/{reply}', 'RepliesController@update')->middleware('auth');

        Route::post('/threads/{channel}/{thread}/subscriptions', 'SubscriptionsController@store')->middleware('auth');

        Route::post('/replies/{reply}/favorites', 'FavoritesController@store')->middleware('auth');

        Route::get('/profile/{user}', 'ProfileController@show');

        Route::get('/notifications', 'UserNotificationsController@index');
        Route::delete('/notifications/{notification}', 'UserNotificationsController@destroy');
    }
);


Route::post('login', 'Auth\APILoginController@login');
// Route::post('logout', 'Auth\LoginController@logout');

// Registration Routes...
Route::post('register', 'Auth\RegisterController@register');

// Password Reset Routes...
Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail');
Route::post('password/reset', 'Auth\ResetPasswordController@reset');
