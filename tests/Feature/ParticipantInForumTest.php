<?php

namespace Tests\Feature;

use App\Reply;
use App\Thread;
use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ParticipantInForum extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function an_unauthenticated_user_cannot_post_reply_in_a_thread()
    {

        $this->withoutExceptionHandling()
            ->expectException('Illuminate\Auth\AuthenticationException');

        $thread = factory(Thread::class)->create();

        $reply = factory(Reply::class)->make();

        $this->post(action('RepliesController@store', $thread), $reply->toArray());

        $this->assertDatabaseMissing('replies', $reply);
    }

    /** @test */
    public function an_authenticated_user_can_post_reply_in_a_thread()
    {
        $this->withoutExceptionHandling();

        $this->be($user = factory(User::class)->create());
        $thread = factory(Thread::class)->create();

        $reply = factory(Reply::class)->make();


        $this->post(action('RepliesController@store', $thread), $reply->toArray())
            ->assertOk()
            ->assertSee($reply->body);
    }

    // TODO: fix
    /** @test */
    public function reply_can_be_deleted_by_reply_owner()
    {
        $this->withoutExceptionHandling();

        $this->signIn($user = factory(User::class)->create());
        $reply = factory(Reply::class)->create([
            'user_id' => $user->id
        ]);

        $this->delete(action('RepliesController@destroy', $reply))
            ->assertStatus(204);

        $this->assertDatabaseMissing('replies', $reply->attributesToArray());
    }

    /** @test */
    public function reply_cannot_be_deleted_by_other_users()
    {
        $this->withoutExceptionHandling()
            ->expectException('Illuminate\Auth\Access\AuthorizationException');

        $this->signIn();
        $reply = factory(Reply::class)->create();

        $this->delete(action('RepliesController@destroy', $reply))
            ->assertStatus(204);

        $this->assertDatabaseHas('replies', $reply->attributesToArray());
    }
}
