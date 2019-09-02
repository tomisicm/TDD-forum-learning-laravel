<?php

namespace Tests\Unit;

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
    }
}
