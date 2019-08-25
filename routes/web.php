<?php

Route::get('/threads', 'ThreadController@index');
Route::get('/threads/{channel}', 'ThreadController@index');
Route::get('/threads/{channel}/{thread}', 'ThreadController@show');

Route::post('/threads/{channel}', 'ThreadController@store');

Route::post('/threads/{thread}/replies', 'RepliesController@store')->middleware('auth');

Auth::routes();
