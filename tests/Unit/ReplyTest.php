<?php

namespace Tests\Unit;

use App\Reply;
use App\User;
use App\Thread;
use Carbon\Carbon;
use Tests\TestCase;

use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ReplyTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function reply_belongs_to_user()
    {
        $reply = factory(Reply::class)->create();

        $this->assertInstanceOf(User::class, $reply->creator);
    }

    /** @test */
    public function reply_belongs_to_thread()
    {
        $reply = factory(Reply::class)->create();

        $this->assertInstanceOf(Thread::class, $reply->thread);
    }

    /** @test */
    public function reply_was_published_during_last_minute()
    {
        $reply = factory(Reply::class)->create();

        $this->assertTrue($reply->wasPublishedAgo(Carbon::now()->subMinute()));

        $reply->created_at = Carbon::now()->subMonth();

        $this->assertFalse($reply->wasPublishedAgo());
    }
}
