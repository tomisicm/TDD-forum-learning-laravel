<?php

namespace Tests\Unit;

use App\Channel;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

use App\User;
use App\Thread;
use App\Reply;

class ThreadTest extends TestCase
{
    use DatabaseMigrations;

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

        $thread->add_reply(factory(Reply::class)->make()->toArray());

        $this->assertCount(1, $thread->replies);
    }
}
