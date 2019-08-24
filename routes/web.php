<?php

Route::get('/threads', 'ThreadController@index');

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
