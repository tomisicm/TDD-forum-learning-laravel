<?php

namespace Tests\Feature;

use App\User;
use App\Thread;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ThreadsTest extends TestCase
{
    use DatabaseMigrations, RefreshDatabase;

    /** @test */
    public function a_user_can_get_threads()
    {
        $user = factory(User::class)->create();

        $thread = factory(Thread::class)->create();

        $this->actingAs($user)->get(action('ThreadController@index'))
            ->assertOk()
            ->assertSee($thread->id)
            ->assertSee($thread->user_id)
            ->assertSee($thread->title)
            ->assertSee($thread->body)
            ->assertJson([$thread->toArray()]);
        // TODO there has to be cleaner than putting [] in assert

        $this->actingAs($user)->get(action('ThreadController@show', $thread))
            ->assertOk()
            ->assertSee($thread->id)
            ->assertSee($thread->user_id)
            ->assertSee($thread->title)
            ->assertSee($thread->body)
            ->assertJson($thread->toArray());
    }
}
