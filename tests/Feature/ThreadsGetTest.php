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

class ThreadsGetTest extends TestCase
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
        // TODO: thread needs channel for redirection
        unset($this->thread->channel);

        $this->get(action('ThreadController@index', ''))
            ->assertOk()
            ->assertSee($this->thread->id)
            ->assertSee($this->thread->user_id)
            ->assertSee($this->thread->title)
            ->assertSee($this->thread->body)
            ->assertJson([$this->thread->toArray()]);
        // TODO there has to be cleaner than putting [] in assert

    }

    // TODO: tmrw
    public function a_user_can_get_threads_for_a_given_channel()
    {
        $this->withoutExceptionHandling();

        $other_thread = factory(Thread::class)->create();

        // dd('/threads/' . $this->thread->channel->slug);

        $resp = $this->get(action('ThreadController@index', $this->thread->channel->slug));

        // dd(json_decode($resp->content()));
        //->assertCount(1, $this->thread);
    }

    /** @test */
    public function a_user_can_get_filter_threads_by_any_username()
    {
        $this->withoutExceptionHandling();

        $this->signIn(create(User::class, ['name' => 'Johnn']));

        $johnsThread = create(Thread::class, ['user_id' => auth()->id()]);

        $otherThread = create(Thread::class);

        $this->get(action('ThreadController@index', ['?by=Johnn']))
            ->assertSee($johnsThread->title)
            ->assertDontSee($otherThread->title);
    }

    /** @test */
    // TODO: FALSE POSITIVE !
    public function a_user_can_get_single_thread()
    {
        $this->signIn();

        unset($this->thread->channel);

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
}
