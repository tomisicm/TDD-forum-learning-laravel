<?php

namespace Tests\Unit;

use App\User;
use App\Reply;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_has_its_most_recent_reply()
    {
        $user = create(User::class);

        $reply = create(Reply::class, [
            'user_id' => $user->id
        ]);

        $this->assertInstanceOf(Reply::class, $user->latestReply);
        $this->assertEquals($reply->id, $user->latestReply->id);
    }
}
