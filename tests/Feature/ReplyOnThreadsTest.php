<?php

namespace Tests\Feature;

use App\Reply;
use App\Thread;
use App\User;
use Tests\TestCase;
use Tests\Traits\AttachJwtToken;

use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;


class ReplyOnThreads extends TestCase
{
    use RefreshDatabase, AttachJwtToken;

    /** @test */
    public function an_unauthenticated_user_cannot_post_reply_in_a_thread()
    {
        $this->withoutExceptionHandling()
            ->expectException('Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException');

        $thread = factory(Thread::class)->create();

        $reply = factory(Reply::class)->make();

        $this->post(action('RepliesController@store', $thread), $reply->toArray());

        $this->assertDatabaseMissing('replies', $reply);
    }

    /** @test */
    public function an_authenticated_user_can_post_reply_in_a_thread()
    {
        $this->loginAs($user = create(User::class));

        $thread = factory(Thread::class)->create();

        $reply = factory(Reply::class)->make([
            'user_id' => $user->id
        ]);

        $this->post(action('RepliesController@store', $thread), $reply->toArray())
            ->assertStatus(201)
            ->assertSee($reply->body);
    }

    /** @test */
    public function reply_can_be_deleted_by_reply_owner()
    {
        $this->loginAs($user = create(User::class));
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
        $this->loginAs(create(User::class))->withoutExceptionHandling()
            ->expectException('Illuminate\Auth\Access\AuthorizationException');

        $reply = factory(Reply::class)->create();

        $this->delete(action('RepliesController@destroy', $reply))
            ->assertStatus(403);

        $this->assertDatabaseHas('replies', $reply->attributesToArray());
    }

    /** @test */
    public function reply_can_be_updated_by_reply_creator()
    {
        $this->loginAs($user = create(User::class));

        $reply = factory(Reply::class)->create([
            'user_id' => $user->id
        ]);

        $this->patch(action('RepliesController@update', $reply), ['body' => 'body changed'])
            ->assertOk();

        $this->assertDatabaseHas('replies', [
            'id' => $reply->id,
            'body' => 'body changed'
        ]);
    }

    /** @test */
    public function reply_cannot_be_updated_by_other_users()
    {
        $reply = factory(Reply::class)->create();

        $this->loginAs(create(User::class));
        $this->withoutExceptionHandling()
            ->expectException('Illuminate\Validation\ValidationException');

        $this->patch(action('RepliesController@update', $reply));

        $this->assertDatabaseHas('replies', $reply->attributesToArray());
    }

    /** @test */
    public function reply_cannot_contain_spam()
    {
        $this->loginAs($user = create(User::class))
            ->withoutExceptionHandling()
            ->expectException(\Exception::class);

        $thread = factory(Thread::class)->create();

        $reply = factory(Reply::class)->make([
            'body' => 'ITS SPAM'
        ]);

        // TODO: return 429 status code
        $this->post(action('RepliesController@store', $thread), $reply->toArray())
            ->assertStatus(403);

        $this->assertDatabaseMissing('replies', $reply->attributesToArray());
    }

    /** @test */
    public function an_authenticated_user_can_post_reply_once_per_minute()
    {
        $this->loginAs($user = create(User::class));

        $reply = factory(Reply::class)->create([
            'user_id' => $user->id
        ]);
        $this->assertCount(1, Reply::all());

        $this->post(action('RepliesController@store', $reply->thread), $reply->toArray())
            ->assertStatus(429);

        $this->assertCount(1, Reply::all());
    }
}
