<?php

namespace Tests\Feature;

use App\Reply;
use App\User;
use Tests\TestCase;
use Tests\Traits\AttachJwtToken;

use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;


class FavoritesTest extends TestCase
{
    use RefreshDatabase, AttachJwtToken;

    /** @test */
    public function an_unauthenticated_user_cannot_favour_anything()
    {
        $this->withoutExceptionHandling()
            ->expectException('Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException');

        $reply = factory(Reply::class)->create();

        $this->post(action('FavoritesController@store', $reply))->assertStatus(303);
    }

    /** @test */
    public function an_authenticated_user_can_favour_any_reply()
    {
        $this->loginAs(create(User::class));

        $reply = factory(Reply::class)->create();

        $this->post(action('FavoritesController@store', $reply));

        $this->assertCount(1, $reply->favorites);
    }

    /** @test */
    public function an_authenticated_user_can_unfavorite_reply()
    {
        $this->withoutExceptionHandling();

        $this->loginAs(create(User::class));

        $reply = factory(Reply::class)->create();

        $this->post(action('FavoritesController@store', $reply));

        $this->post(action('FavoritesController@store', $reply))->assertOk();

        $this->assertCount(0, $reply->favorites);
    }
}
