<?php

namespace Tests\Unit;

use App\User;
use App\Activity;
use App\Favorite;
use App\Reply;
use App\Thread;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ActivityTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function activity_is_recorded_when_thread_is_created()
    {
        $this->signIn();
        $thread = create(Thread::class);

        $this->assertDatabaseHas('activities', [
            'user_id' => auth()->id(),
            'type' => strtolower((new \ReflectionClass($thread))->getShortname()) . '_created',
            'subject_id' => $thread->id,
            'subject_type' => get_class($thread)
        ]);

        $activity = Activity::first();

        $this->assertEquals($activity->subject->id, $thread->id);
    }


    /** @test */
    public function activity_is_recorded_when_reply_is_created()
    {
        $this->signIn();
        $reply = create(Reply::class);

        // $this->assertDatabaseHas('activities', [
        //     'user_id' => $reply->user->id,
        //     'type' => strtolower((new \ReflectionClass($reply))->getShortname()) . '_created',
        //     'subject_id' => $reply->id,
        //     'subject_type' => get_class($reply)
        // ]);

        $activity = Activity::latest()->first();

        $this->assertEquals(2, Activity::count());
        $this->assertEquals($activity->subject->id, $reply->id);
    }

    /** @test */
    public function activity_is_deleted_when_reply_is_deleted()
    {
        $this->signIn($user = factory(User::class)->create());
        $reply = factory(Reply::class)->create([
            'user_id' => $user->id
        ]);

        $this->delete(action('RepliesController@destroy', $reply));

        $this->assertEquals(1, Activity::count());
    }

    // TODO
    /** @test */
    public function favourites_are_deleted_when_reply_is_deleted()
    {
        $this->signIn($user = factory(User::class)->create());
        $reply = factory(Reply::class)->create([
            'user_id' => $user->id
        ]);

        $this->post(action('FavoritesController@store', $reply));
        $this->delete(action('RepliesController@destroy', $reply));

        $this->assertCount(0, Favorite::all());
    }

    /** @test */
    public function user_has_activity_feed()
    {
        $this->signIn();
        $reply = create(Reply::class);

        // $this->assertDatabaseHas('activities', [
        //     'user_id' => $reply->user->id,
        //     'type' => strtolower((new \ReflectionClass($reply))->getShortname()) . '_created',
        //     'subject_id' => $reply->id,
        //     'subject_type' => get_class($reply)
        // ]);

        $activity = Activity::latest()->first();

        $this->assertEquals(2, Activity::count());
        $this->assertEquals($activity->subject->id, $reply->id);
    }
}