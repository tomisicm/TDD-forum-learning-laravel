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
    use RefreshDatabase;

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

        $this->get(action('ThreadController@index', ''))
            ->assertOk()
            ->assertSee($this->thread->id)
            ->assertSee($this->thread->user_id)
            ->assertSee($this->thread->title)
            ->assertSee($this->thread->body)
            ->assertJson([$this->thread->toArray()]);
        // TODO there has to be cleaner than putting [] in assert

    }

    /** @test */
    public function a_user_can_get_threads_for_a_given_channel()
    {
        $this->withoutExceptionHandling();

        $threadNotInChannel = factory(Thread::class)->create();

        $resp = $this->get(action('ThreadController@index', $this->thread->channel->slug));

        $this->assertCount(1, json_decode($resp->content(), true));

        // $thread = json_decode($resp->content());
        // dd($thread->chanel);
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
            ->assertJson($this->thread->toArray());
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
    public function a_user_can_filter_threads_by_replies_count()
    {
        $this->signIn();

        // TODO: this thing is creating 2 threads instead of one
        $threadWithTwoReplies = create(Thread::class);
        create(Reply::class, ['thread_id' => $threadWithTwoReplies->id], 2);


        $response = $this->getJson(action('ThreadController@index', ['?popular=1']))->json();

        $this->assertEquals([2, 0, 0, 0], array_column($response, 'replies_count'));
    }
}
