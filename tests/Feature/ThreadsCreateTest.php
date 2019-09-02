<?php

namespace Tests\Feature;

use App\Reply;
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

    // TODO: dedicated method for getting only thread attributes
    /** @test */
    public function an_unauthorized_user_cannot_delete_thread()
    {
        $thread = create(Thread::class);
        $attributes = [
            'id' => $thread->id,
            'title' => $thread->title,
            'body' => $thread->body
        ];
        $this->signIn();

        $this->delete(action('ThreadController@destroy', [$thread->channel->name, $thread->id]))
            ->assertStatus(403);

        $this->assertDatabaseHas('threads', $attributes);
    }

    // TODO: dedicated method for getting only thread attributes
    /** @test */
    public function thread_creator_can_delete_thread()
    {
        $thread = create(Thread::class);

        $attributes = [
            'id' => $thread->id,
            'title' => $thread->title,
            'body' => $thread->body
        ];

        $this->signIn($thread->creator);

        $this->delete(action('ThreadController@destroy', [$thread->channel->name, $thread->id]))
            ->assertStatus(204);

        $this->assertDatabaseMissing('threads', $attributes);
    }


    public function thread_can_be_deleted_by_users_with_permission()
    {
        // TODO:
    }

    /** @test */
    public function thread_deletion_removes_replies()
    {
        $reply = create(Reply::class);
        $attributes = $reply->toArray();
        $this->signIn($reply->thread->creator);

        $this->delete(action('ThreadController@destroy', [$reply->thread->channel->name, $reply->thread->id]))
            ->assertStatus(204);

        $this->assertDatabaseMissing('replies', $attributes);
    }
}
