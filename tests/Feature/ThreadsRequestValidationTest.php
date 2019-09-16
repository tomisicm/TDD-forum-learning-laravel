<?php

namespace Tests\Feature;

use App\User;
use App\Thread;

use Tests\TestCase;
use Tests\Traits\AttachJwtToken;

use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ThreadsRequestValidationTests extends TestCase
{
    use RefreshDatabase, AttachJwtToken;

    /** @test */
    public function thread_requires_title_body_channelId()
    {
        $this->loginAs(factory(User::class)->create());

        $thread = factory(Thread::class)->make([
            'title' => null,
            'body' => null,
            'user_id' => null
        ]);

        $this->post(action('ThreadController@store', [$thread->channel]), $thread->toArray())
            ->assertSessionHasErrors('title')
            ->assertSessionHasErrors('body');
    }

    /** @test */
    public function thread_requires_valid_channel()
    {
        $this->withoutExceptionHandling()
            ->expectException('Illuminate\Database\Eloquent\ModelNotFoundException');

        $this->signIn(factory(User::class)->create());

        $thread = factory(Thread::class)->make([
            'user_id' => null
        ]);

        $this->post(action('ThreadController@store', ['channel' => 5]), $thread->toArray())
            ->assertSessionHasErrors('channel_id');
    }
}
