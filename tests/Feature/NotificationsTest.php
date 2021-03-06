<?php

namespace Tests\Feature;

use Tests\TestCase;
use Tests\Traits\AttachJwtToken;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Thread;
use App\Reply;
use Illuminate\Notifications\DatabaseNotification;

class NotificationsTest extends TestCase
{
    use RefreshDatabase, AttachJwtToken;

    public function setUp(): void
    {
        parent::setUp();

        $this->thread = create(Thread::class);

        $this->signIn();
    }

    /** @test */
    public function thread_subscribers_will_get_notification_when_somebody_replies_to_thread()
    {
        $this->thread->subscribe(auth()->id());

        $this->assertCount(0, auth()->user()->notifications);

        $this->thread->addReply(make(Reply::class, [
            'thread_id' => $this->thread->id,
            'user_id' => $this->thread->user_id
        ])->attributesToArray());

        $this->assertCount(1, auth()->user()->fresh()->notifications);

        $this->assertDatabaseHas('notifications', [
            'type' => 'App\\Notifications\\ThreadWasUpdated',
            'notifiable_type' => get_class(auth()->user()),
            'notifiable_id' => auth()->id()
        ]);
    }

    /** @test */
    public function thread_subscribers_will_not_get_notification_when_self_replying_to_thread()
    {
        $this->thread->subscribe(auth()->id());

        $this->assertCount(0, auth()->user()->notifications);

        $this->thread->addReply(make(Reply::class, [
            'thread_id' => $this->thread->id,
            'user_id' => auth()->id()
        ])->attributesToArray());

        $this->assertDatabaseMissing('notifications', [
            'type' => 'App\\Notifications\\ThreadWasUpdated',
            'notifiable_type' => get_class(auth()->user()),
            'notifiable_id' => auth()->id()
        ]);

        $this->assertCount(0, auth()->user()->fresh()->notifications);
    }

    /** @test */
    public function user_can_markAsRead_notifications()
    {
        $this->loginAs(auth()->user());

        create(DatabaseNotification::class);

        $userNotification = auth()->user()->refresh()->unreadNotifications()->first();

        $this->delete(action('UserNotificationsController@destroy', [$userNotification]));

        $this->assertDatabaseHas('notifications', [
            'type' => 'App\\Notifications\\ThreadWasUpdated',
            'notifiable_type' => get_class(auth()->user()),
            'notifiable_id' => auth()->id(),
            'read_at' => date('Y-m-d H:i:s')
        ]);
    }

    /** @test */
    public function thread_subscribers_fetch_notifications()
    {
        create(DatabaseNotification::class);

        $resp = $this->getJson(action('UserNotificationsController@index'))->json();

        $this->assertCount(1, $resp);
    }
}
