<?php

namespace App\Http\Controllers;

use App\Channel;
use App\Thread;
use App\User;

use Illuminate\Http\Request;

class ThreadController extends Controller
{
    /**
     * index
     *
     * @param  mixed $channel Channel
     *
     * @return void
     */
    public function index(Channel $channel)
    {
        if ($channel->exists) {
            $threads =  $channel->threads()->latest()->get();
        } else {
            $threads = Thread::latest();
        }

        if ($username = request('by')) {
            $user = User::where('name', $username)->firstOrFail();

            $threads->where('user_id', $user->id);
        }

        $threads = $threads->get();

        return $threads;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Channel $channel)
    {
        $attributes = $this->validate($request, [
            'title' => 'required|min:3',
            'body' => 'required|max:512'
        ]);
        $attributes['user_id'] = auth()->id();
        $attributes['channel_id'] = $channel->id;

        $thread = Thread::create($attributes);

        return $thread;
    }

    /**
     * Display the specified resource.
     *
     * @param  mixed $channel
     * @param  mixed $thread
     *
     * @return void
     */
    public function show($channelSlug, Thread $thread)
    {
        // $channelId = Channel::whereSlug($channelSlug)->first()->id;
        // $thread = Thread::where('channel_id', $channelId)->get();

        return $thread->with(['channel', 'creator', 'replies.user'])->get();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Thread  $thread
     * @return \Illuminate\Http\Response
     */
    public function edit(Thread $thread)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Thread  $thread
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Thread $thread)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Thread  $thread
     * @return \Illuminate\Http\Response
     */
    public function destroy(Thread $thread)
    {
        //
    }
}
