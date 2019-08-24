<?php

Route::get('/threads', 'ThreadController@index');
Route::get('/threads/{thread}', 'ThreadController@show');
Route::post('/threads', 'ThreadController@store');

Route::post('/threads/{thread}/replies', 'RepliesController@store')->middleware('auth');

Auth::routes();
