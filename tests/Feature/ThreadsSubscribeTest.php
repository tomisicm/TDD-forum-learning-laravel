<?php

namespace Tests\Feature;

use Tests\TestCase;
use Tests\Traits\AttachJwtToken;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

use App\User;
use App\Thread;
use App\Reply;
use App\ThreadSubscription;

class ThreadsSubscribeTest extends TestCase
{
    use RefreshDatabase, AttachJwtToken;

    /** @test */
    public function a_user_can_subscribe_to_threads()
    {
        $thread = create(Thread::class);

        $this->loginAs(create(User::class));

        $this->post(action('SubscriptionsController@store', [$thread->id]))
            ->assertOk();

        $this->assertDatabaseHas('thread_subscriptions', [
            'thread_id' => $thread->id,
            'user_id' => auth()->id()
        ]);
    }

    /** @test */
    public function a_user_can_unsubscribe_to_threads()
    {
        $thread = create(Thread::class);

        $this->loginAs($user = create(User::class));

        $thread->subscribe($user->id);

        $this->post(action('SubscriptionsController@store', [$thread->id]))
            ->assertOk();

        $this->assertDatabaseMissing('thread_subscriptions', [
            'thread_id' => $thread->id,
            'user_id' => auth()->id()
        ]);
    }
}
