<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Notifications\ThreadWasUpdated;

class UserNotificationsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return auth()->user()->unreadNotifications;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Notifications\ThreadWasUpdated $notification
     * @return \Illuminate\Http\Response
     */
    public function destroy($notification)
    {
        auth()->user()->unreadNotifications()->findOrFail($notification)->markAsRead();
    }
}
