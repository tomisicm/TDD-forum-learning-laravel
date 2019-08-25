<?php

namespace Tests\Feature;

use App\User;
use App\Thread;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ThreadsRequestValidationTests extends TestCase
{
    use DatabaseMigrations, RefreshDatabase;

    /** @test */
    public function thread_requires_title_body_channelId()
    {
        $this->signIn(factory(User::class)->create());

        $thread = factory(Thread::class)->make([
            'title' => null,
            'body' => null,
            'channel_id' => null,
            'user_id' => null
        ]);

        $this->post(action('ThreadController@store'), $thread->toArray())
            ->assertSessionHasErrors('title')
            ->assertSessionHasErrors('body')
            ->assertSessionHasErrors('channel_id');
    }
}
