<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Inspections\Spam;

class SpamTest extends TestCase
{
    /** @test */
    public function it_validates_spam_and_returns_false_if_no_spam_detected()
    {
        $spam = new Spam();

        $this->assertFalse($spam->detect('Just some random text'));
    }

    /** @test */
    public function it_validates_invalid_keywords()
    {
        $this->expectException(\Exception::class);

        $spam = new Spam();

        $this->assertFalse($spam->detect('ITS SPAM'));
    }

    /** @test */
    public function it_validates_invalid_repeating_characters()
    {
        $this->expectException(\Exception::class);

        $spam = new Spam();

        $this->assertFalse($spam->detect('HELLO AAAAAAAAAAAAAAAAAAAAAAA'));
    }
}
