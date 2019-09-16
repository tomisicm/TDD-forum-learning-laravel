<?php

namespace Tests\Feature;

use App\Activity;
use App\Reply;
use App\User;
use App\Thread;

use Tests\TestCase;
use Tests\Traits\AttachJwtToken;

use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ThreadsCreateTest extends TestCase
{
    use RefreshDatabase, AttachJwtToken;


    /** @test */
    public function an_unauthenticated_user_cannot_post()
    {
        $this->withoutExceptionHandling()
            ->expectException('Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException');

        $thread = factory(Thread::class)->make();

        $this->post(action('ThreadController@store', $thread->channel->name), $thread->attributesToArray());

        $this->assertDatabaseMissing('threads', $thread->attributesToArray());
    }

    /** @test */
    public function authenticated_user_can_create_thread()
    {

        $this->loginAs($user = factory(User::class)->create());

        $thread = factory(Thread::class)->make([
            'user_id' => $user->id
        ]);

        $slug = $thread->channel->slug;

        $this->post(action('ThreadController@store', $slug), $thread->attributesToArray())
            ->assertStatus(201)
            ->assertSee($thread->id)
            ->assertSee($thread->title)
            ->assertSee($thread->body)
            ->assertJson($thread->attributesToArray());

        $this->assertDatabaseHas('threads', $thread->attributesToArray());
    }

    /** @test */
    public function an_unauthorized_user_cannot_delete_thread()
    {
        $this->withoutExceptionHandling()
            ->expectException('Illuminate\Auth\Access\AuthorizationException');

        $this->loginAs(create(User::class));

        $thread = create(Thread::class);

        $this->delete(action('ThreadController@destroy', [$thread->channel->name, $thread->id]))
            ->assertStatus(403);

        $this->assertDatabaseHas('threads', $thread->attributesToArray());
    }

    /** @test */
    public function thread_creator_can_delete_thread()
    {
        $thread = create(Thread::class);

        $this->loginAs($thread->creator);

        $this->delete(action('ThreadController@destroy', [$thread->channel->name, $thread->id]))
            ->assertStatus(204);

        $this->assertDatabaseMissing('threads', $thread->attributesToArray());
    }

    /** @test */
    public function when_thread_is_deleted_all_activities_are_deleted()
    {
        $reply = create(Reply::class);
        $threadActivity = $reply->thread->activity->first()->attributesToArray();
        $replyActivity = $reply->activity->first()->attributesToArray();

        $this->loginAs($reply->thread->creator);

        $this->delete(action('ThreadController@destroy', [$reply->thread->channel->name, $reply->thread->id]))
            ->assertStatus(204);

        $this->assertDatabaseMissing('activities', $threadActivity);
        $this->assertDatabaseMissing('activities', $replyActivity);

        $this->assertCount(0, Activity::all());
    }


    public function thread_can_be_deleted_by_users_with_permission()
    {
        // TODO:
    }

    /** @test */
    public function thread_deletion_removes_replies()
    {
        $reply = create(Reply::class);

        $this->loginAs($reply->thread->creator);

        $this->delete(action('ThreadController@destroy', [$reply->thread->channel->name, $reply->thread->id]))
            ->assertStatus(204);

        $this->assertDatabaseMissing('replies', $reply->attributesToArray());
    }
}
