<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Thread;
use App\Reply;

class NotificationsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function thread_subscribers_will_get_notification_when_somebody_replies_to_thread()
    {
        $thread = create(Thread::class);
        $this->signIn();

        $thread->subscribe(auth()->id());

        $this->assertCount(0, auth()->user()->notifications);

        $thread->addReply(make(Reply::class, [
            'thread_id' => $thread->id,
            'user_id' => $thread->creator->id
        ])->attributesToArray());

        $this->assertDatabaseHas('notifications', [
            'type' => 'App\\Notifications\\ThreadWasUpdated',
            'notifiable_type' => get_class(auth()->user()),
            'notifiable_id' => auth()->id()
        ]);

        $this->assertCount(1, auth()->user()->fresh()->notifications);
    }

    /** @test */
    public function thread_subscribers_will_not_get_notification_when_self_replying_to_thread()
    {
        $thread = create(Thread::class);
        $this->signIn();

        $thread->subscribe(auth()->id());

        $this->assertCount(0, auth()->user()->notifications);

        $thread->addReply(make(Reply::class, [
            'thread_id' => $thread->id,
            'user_id' => auth()->id()
        ])->attributesToArray());

        $this->assertDatabaseMissing('notifications', [
            'type' => 'App\\Notifications\\ThreadWasUpdated',
            'notifiable_type' => get_class(auth()->user()),
            'notifiable_id' => auth()->id()
        ]);

        $this->assertCount(0, auth()->user()->fresh()->notifications);
    }
}
