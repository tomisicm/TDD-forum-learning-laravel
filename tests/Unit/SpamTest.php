<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Spam;

class SpamTest extends TestCase
{
    /** @test */
    public function it_validates_spam()
    {
        $spam = new Spam();

        $this->assertFalse($spam->detect('Just some random text'));
    }

    /** @test */
    public function it_validates_spam_and_throws_error()
    {
        $this->expectException(\Exception::class);

        $spam = new Spam();

        $this->assertFalse($spam->detect('ITS SPAM'));
    }
}
