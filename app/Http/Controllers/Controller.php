<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Tymon\JWTAuth\Facades\JWTAuth;

class Controller extends BaseController
{
    // https://stackoverflow.com/questions/31309098/laravel-testcase-not-sending-authorization-headers-jwt-token
    public function __construct()
    {
        if ((\App::environment() == 'testing') && array_key_exists("HTTP_AUTHORIZATION",  \Request::server())) {
            JWTAuth::setRequest(\Route::getCurrentRequest());
        }
    }

    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
}
