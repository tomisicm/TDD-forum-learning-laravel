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
    use RefreshDatabase;


    /** @test */
    public function an_unauthenticated_user_cannot_post()
    {
        $this->withoutExceptionHandling()
            ->expectException('Illuminate\Auth\AuthenticationException');

        $thread = factory(Thread::class)->make();

        $this->post(action('ThreadController@store', $thread->channel->id), $thread->toArray());

        $this->assertDatabaseMissing('threads', $thread->toArray());
    }

    /** @test */
    public function an_unauthenticated_user_cannot_delete()
    {
        $this->withoutExceptionHandling()
            ->expectException('Illuminate\Auth\AuthenticationException');

        $thread = factory(Thread::class)->make();

        $this->post(action('ThreadController@destroy', [$thread->channel->name, $thread->id]), $thread->toArray());

        $this->assertDatabaseMissing('threads', $thread->toArray());
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

        $this->assertDatabaseHas('threads', $thread->toArray());
    }

    /** @test */
    public function thread_creator_can_delete_thread()
    {
        $thread = create(Thread::class);
        $attributes = $thread->toArray();
        $this->signIn($thread->creator);

        $this->delete(action('ThreadController@destroy', [$thread->channel->name, $thread->id]))
            ->assertStatus(204);

        $this->assertDatabaseMissing('threads', $attributes);
    }

    /** @test */
    public function thread_deletion_removes_replies()
    {
        // $thread = create(Thread::class);
        // $attributes = $thread->toArray();
        // $this->signIn($thread->creator);

        // $this->delete(action('ThreadController@destroy', [$thread->channel->name, $thread->id]))
        //     ->assertStatus(204);

        // $this->assertDatabaseMissing('threads', $attributes);
    }
}
