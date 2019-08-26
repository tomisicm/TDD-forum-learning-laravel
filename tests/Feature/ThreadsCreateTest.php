<?php

namespace Tests\Feature;

use App\User;
use App\Thread;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ThreadsCreateTest extends TestCase
{
    use DatabaseMigrations, RefreshDatabase;


    /** @test */
    public function an_unauthenticated_user_cannot_post()
    {
        $this->withoutExceptionHandling()
            ->expectException('Illuminate\Auth\AuthenticationException');

        $thread = factory(Thread::class)->make();
        unset($thread->user_id);

        $this->post(action('ThreadController@store', $thread->channel->id), $thread->toArray())
            ->assertStatus(201)
            ->assertSee($thread->id)
            ->assertSee($thread->title)
            ->assertSee($thread->body)
            ->assertJson($thread->toArray());
    }

    /** @test */
    public function authenticated_user_can_create_thread()
    {
        $this->signIn(factory(User::class)->create());

        $thread = factory(Thread::class)->make();
        unset($thread->user_id);

        $slug = $thread->channel->slug;

        unset($thread->channel);

        $this->post(action('ThreadController@store', $slug), $thread->toArray())
            ->assertStatus(201)
            ->assertSee($thread->id)
            ->assertSee($thread->title)
            ->assertSee($thread->body)
            ->assertJson($thread->toArray());

        // TODO EXTRACT METHOD FOR GETTING THING
    }
}
