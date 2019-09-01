<?php

namespace Tests\Feature;

use App\Thread;
use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProfilesTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_has_a_profile()
    {
        $user = create(User::class);

        $this->get(action('ProfileController@show', $user->name))
            ->assertSee($user->name);
    }

    /** @test */
    // TODO: user is not needed since i am on the profile page
    public function a_profile_page_displays_threads()
    {
        $thread = create(Thread::class);

        $this->get(action('ProfileController@show', $thread->creator->name))
            ->assertSee($thread->title)
            ->assertSee($thread->body);
    }
}
