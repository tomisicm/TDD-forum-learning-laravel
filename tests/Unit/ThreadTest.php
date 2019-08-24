<?php

namespace Tests\Unit;

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
    public function thread_has_many_replies()
    {
        $reply = factory(Reply::class)->create();

        $this->assertInstanceOf(Thread::class, $reply->thread);
    }
}
