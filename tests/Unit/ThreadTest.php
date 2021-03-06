<?php

namespace Tests\Unit;

use App\Channel;
use App\Notifications\ThreadWasUpdated;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

use App\User;
use App\Thread;
use App\Reply;
use App\ThreadSubscription;
use Illuminate\Support\Facades\Notification;

class ThreadTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function thread_belongs_to_user()
    {
        $thread = factory(Thread::class)->create();

        $this->assertInstanceOf(User::class, $thread->creator);
    }

    /** @test */
    public function thread_belongs_to_channel()
    {
        $thread = factory(Thread::class)->create();

        $this->assertInstanceOf(Channel::class, $thread->channel);
    }

    /** @test */
    public function thread_has_many_replies()
    {
        $reply = factory(Reply::class)->create();

        $this->assertInstanceOf(Thread::class, $reply->thread);
    }

    /** @test */
    public function thread_can_add_reply()
    {
        $thread = factory(Thread::class)->create();

        $thread->addReply(factory(Reply::class)->make()->toArray());

        $this->assertCount(1, $thread->replies);
    }

    /** @test */
    public function thread_can_be_subscribed_to()
    {
        $thread = create(Thread::class);

        $this->signIn();

        $thread->subscribe();

        $getSubscribedUser = $thread->subscriptions()->where('user_id', auth()->id())->get();

        $this->assertCount(1, $getSubscribedUser);
    }

    /** @test */
    public function thread_can_be_subscribed_to_by_given_userId()
    {
        $thread = create(Thread::class)->subscribe($userId = 1);;

        $getSubscribedUser = $thread->subscriptions()->where('user_id', $userId)->get();

        $this->assertCount(1, $getSubscribedUser);
    }

    /** @test */
    public function thread_can_be_unsubscribed()
    {
        $this->signIn();

        $thread = create(Thread::class)->subscribe(auth()->id());

        $thread->unsubscribe(auth()->id());

        $this->assertCount(0, $thread->subscriptions()->get());
    }

    /** @test */
    public function thread_notifies_subscribed_users_when_update_occurs()
    {
        Notification::fake();

        $thread = create(Thread::class);

        $this->signIn();

        $thread->subscribe(auth()->id())->addReply([
            'body' => 'Adding Reply',
            'user_id' => $thread->creator->id
        ]);


        Notification::assertSentTo(auth()->user(), ThreadWasUpdated::class);
    }
}
