<?php

namespace Tests\Feature;

use App\Reply;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class FavoritesTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function an_unauthenticated_user_cannot_favour_anything()
    {
        $this->withoutExceptionHandling()
            ->expectException('Illuminate\Auth\AuthenticationException');

        $reply = factory(Reply::class)->create();

        $this->post(action('FavoritesController@store', $reply));
    }

    /** @test */
    public function an_authenticated_user_can_favour_any_reply()
    {
        $this->withoutExceptionHandling();

        $this->signIn();

        $reply = factory(Reply::class)->create();

        $this->post(action('FavoritesController@store', $reply));

        $this->assertCount(1, $reply->favorites);
    }

    /** @test */
    public function an_authenticated_user_can_favour_item_only_once()
    {
        $this->signIn();

        $reply = factory(Reply::class)->create();

        try {
            $this->post(action('FavoritesController@store', $reply));
            $this->post(action('FavoritesController@store', $reply));
        } catch (\Exception $e) {
            $this->fail('One favorite only');
        }
        $this->assertCount(1, $reply->favorites);
    }
}
