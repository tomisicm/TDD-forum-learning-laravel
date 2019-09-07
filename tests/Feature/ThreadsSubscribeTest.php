<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Thread;
use App\ThreadSubscription;

class ThreadsSubscribeTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_can_subscribe_to_threads()
    {
        $thread = create(Thread::class);

        $this->signIn();

        $this->post(action('SubscriptionsController@store', [$thread->channel->name, $thread->id]))
            ->assertOk();

        $this->assertDatabaseHas('thread_subscriptions', [
            'thread_id' => $thread->id,
            'user_id' => auth()->id()
        ]);
    }
}
