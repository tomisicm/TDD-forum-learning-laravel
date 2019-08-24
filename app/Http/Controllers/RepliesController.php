<?php

namespace App\Http\Controllers;

use App\Thread;
use Illuminate\Http\Request;

class RepliesController extends Controller
{
    /**
     * Store a newly created reply in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Thread $thread)
    {
        $attributes = [
            'body' => request('body'),
            'user_id' => auth()->id(),
            'thread_id' => $thread->id
        ];

        $thread->add_reply($attributes);

        return $thread->replies;
    }
}
