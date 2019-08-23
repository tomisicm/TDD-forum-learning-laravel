<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Reply;
use App\User;
use App\Thread;
use Faker\Generator as Faker;

$factory->define(Reply::class, function (Faker $faker) {
    return [
        'thread_id' => factory(Thread::class)->create(),
        'user_id' => factory(User::class)->create(),
        'body' => $faker->paragraph
    ];
});
