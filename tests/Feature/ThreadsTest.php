<?php

namespace Tests\Feature;

use App\Channel;
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
        $this->signIn();

        // TODO: thread needs channel for redirection
        unset($this->thread->channel);

        $this->get(action('ThreadController@index'))
            ->assertOk()
            ->assertSee($this->thread->id)
            ->assertSee($this->thread->user_id)
            ->assertSee($this->thread->title)
            ->assertSee($this->thread->body)
            ->assertJson([$this->thread->toArray()]);
        // TODO there has to be cleaner than putting [] in assert

    }

    /** @test */
    // TODO: FALSE POSITIVE !
    public function a_user_can_get_single_thread()
    {
        $this->signIn();

        $this->get(action('ThreadController@show', [$this->thread->channel->slug, $this->thread]))
            ->assertOk()
            ->assertSee($this->thread->id)
            ->assertSee($this->thread->creator)
            ->assertSee($this->thread->channel->slug)
            ->assertSee($this->thread->channel->name)
            ->assertSee($this->thread->title)
            ->assertSee($this->thread->body)
            ->assertJson([$this->thread->toArray()]);
    }

    /** @test */
    public function a_user_can_get_thread_replies()
    {
        $this->signIn();

        $replies = factory(Reply::class)->create(['thread_id' => $this->thread->id]);

        $this->get(action('ThreadController@show', [$replies->thread->channel, $replies->thread_id]))
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

        $this->post(action('ThreadController@store', $thread->channel->id), $thread->toArray())
            ->assertStatus(201)
            ->assertSee($thread->id)
            ->assertSee($thread->title)
            ->assertSee($thread->body)
            ->assertJson($thread->toArray());
    }

    /** @test */
    public function authenticated_user_can_create_thread()
    {
        $this->signIn(factory(User::class)->create());

        $thread = factory(Thread::class)->make();
        unset($thread->user_id);
        unset($thread->channel);

        $this->post(action('ThreadController@store', $thread->channel_id), $thread->toArray())
            ->assertStatus(201)
            ->assertSee($thread->id)
            ->assertSee($thread->title)
            ->assertSee($thread->body)
            ->assertJson($thread->toArray());

        // TODO EXTRACT METHOD FOR GETTING THING
    }

    /** @test */
    public function a_user_can_filter_threads_according_to_a_tag()
    {
        $this->withoutExceptionHandling();

        $other_thread = factory(Thread::class)->create();

        // dd('/threads/' . $this->thread->channel->slug);

        $resp = $this->get(action('ThreadController@index', $this->thread->channel->slug));

        dd(json_decode($resp->content()));
        //->assertCount(1, $this->thread);
    }
}
