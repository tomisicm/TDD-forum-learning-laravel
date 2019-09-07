<?php

namespace App\Events;

use Illuminate\Queue\SerializesModels;

class ThreadWasUpdated
{
    use SerializesModels;

    public $thread, $reply;

    /**
     * Create a new event instance.
     *
     * @param  \App\Thread  $thread
     * @param  \App\Reply  $reply
     * @return void
     */
    public function __construct($thread, $reply)
    {
        $this->thread = $thread;
        $this->reply = $reply;
    }
}
