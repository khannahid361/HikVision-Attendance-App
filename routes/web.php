<?php

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/hik-test', function () {

    $res = Http::withDigestAuth(
        env('HIKVISION_USERNAME'),
        env('HIKVISION_PASSWORD')
    )->get(
        'http://10.1.1.2/ISAPI/System/status'
    );

    return $res->body();
});
// 'http://' . env('HIKVISION_IP') . ':' . env('HIKVISION_PORT') . '/ISAPI/System/status'