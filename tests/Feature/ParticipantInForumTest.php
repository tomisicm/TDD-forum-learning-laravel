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
}
