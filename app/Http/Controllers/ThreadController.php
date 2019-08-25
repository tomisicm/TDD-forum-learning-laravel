<?php

namespace App\Http\Controllers;

use App\Channel;
use App\Thread;
use Illuminate\Http\Request;

class ThreadController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($channelSlug = null)
    {
        if ($channelSlug) {
            $channelId = Channel::whereSlug($channelSlug)->first()->id;
            $threads = Thread::where('channel_id', $channelId)->get();
            //dd($threads->toArray());
            // dd(Thread::all());
            return $threads;
        }
        return Thread::all();
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
