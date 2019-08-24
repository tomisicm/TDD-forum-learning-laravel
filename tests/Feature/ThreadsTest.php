<?php

namespace Tests\Feature;

use App\Reply;
use App\User;
use App\Thread;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ThreadsTest extends TestCase
{
    use DatabaseMigrations, RefreshDatabase;

    private $thread;

    protected function setUp(): void
    {
        parent::setUp();
        $this->thread = factory(Thread::class)->create();
    }

    /** @test */
    public function a_user_can_get_threads()
    {
        $user = factory(User::class)->create();

        $this->actingAs($user)->get(action('ThreadController@index'))
            ->assertOk()
            ->assertSee($this->thread->id)
            ->assertSee($this->thread->user_id)
            ->assertSee($this->thread->title)
            ->assertSee($this->thread->body)
            ->assertJson([$this->thread->toArray()]);
        // TODO there has to be cleaner than putting [] in assert

    }

    /** @test */
    public function a_user_can_get_single_threads()
    {
        $user = factory(User::class)->create();

        $this->actingAs($user)->get(action('ThreadController@show', $this->thread))
            ->assertOk()
            ->assertSee($this->thread->id)
            ->assertSee($this->thread->creator)
            ->assertSee($this->thread->title)
            ->assertSee($this->thread->body)
            ->assertJson([$this->thread->toArray()]);
    }

    /** @test */
    public function a_user_can_get_thread_replies()
    {
        $user = factory(User::class)->create();

        $replies = factory(Reply::class)->create(['thread_id' => $this->thread->id]);

        $this->actingAs($user)->get(action('ThreadController@show', $replies->thread_id))
            ->assertOk()
            ->assertSee($replies->id)
            ->assertSee($replies->thread->id)
            ->assertSee($replies->body);
    }

    /** @test */
    public function an_unauthenticated_user_cannot_post()
    {
        $this->withoutExceptionHandling()
            ->expectException('Illuminate\Auth\AuthenticationException');

        $thread = factory(Thread::class)->make();
        unset($thread->user_id);

        $this->post(action('ThreadController@store'), $thread->toArray())
            ->assertStatus(201)
            ->assertSee($thread->id)
            ->assertSee($thread->title)
            ->assertSee($thread->body)
            ->assertJson($thread->toArray());
    }

    /** @test */
    public function a_user_can_create_thread()
    {
        $this->withoutExceptionHandling();

        $user = factory(User::class)->create();

        $thread = factory(Thread::class)->make();
        unset($thread->user_id);

        $this->actingAs($user)->post(action('ThreadController@store'), $thread->toArray())
            ->assertStatus(201)
            ->assertSee($thread->id)
            ->assertSee($thread->title)
            ->assertSee($thread->body)
            ->assertJson($thread->toArray());

        // TODO EXTRACT METHOD FOR GETTING THING
    }
}
