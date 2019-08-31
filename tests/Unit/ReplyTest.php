<?php

namespace Tests\Unit;

use App\Reply;
use App\User;
use App\Thread;
use Illuminate\Foundation\Testing\DatabaseMigrations;
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

        $this->assertInstanceOf(User::class, $reply->user);
    }

    /** @test */
    public function reply_belongs_to_thread()
    {
        $reply = factory(Reply::class)->create();

        $this->assertInstanceOf(Thread::class, $reply->thread);
    }
}
