<?php

Route::get('/channels', 'ChannelController@index');

Route::get('/threads', 'ThreadController@index');

Route::get('/threads/{channel}', 'ThreadController@index');
Route::post('/threads/{channel}', 'ThreadController@store')->middleware('auth');
Route::delete('/threads/{channel}/{thread}', 'ThreadController@destroy')->middleware('auth');
Route::get('/threads/{channel}/{thread}', 'ThreadController@show');

Route::get('/threads/{channel}/{thread}/replies', 'RepliesController@index');
Route::post('/threads/{thread}/replies', 'RepliesController@store')->middleware('auth');
Route::delete('/replies/{reply}', 'RepliesController@destroy')->middleware('auth');
Route::patch('/replies/{reply}', 'RepliesController@update')->middleware('auth');

Route::post('/threads/{channel}/{thread}/subscriptions', 'SubscriptionsController@store')->middleware('auth');

Route::post('/replies/{reply}/favorites', 'FavoritesController@store')->middleware('auth');


Route::get('/profile/{user}', 'ProfileController@show');

Auth::routes();
