<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Ziggy — Named Routes in JavaScript
    |--------------------------------------------------------------------------
    |
    | Ziggy exposes your Laravel named routes to the Vue frontend via the
    | `route()` helper. By default all routes are included. You can restrict
    | them here if needed.
    |
    */

    // 'only' => ['feed', 'explore', 'posts.*', 'comments.*', 'profile.*', 'users.*', 'login', 'register', 'logout'],

    'except' => [
        'debugbar.*',
        'horizon.*',
        'telescope.*',
    ],

    'groups' => [],

    'url'    => env('APP_URL', 'http://localhost'),
    'port'   => null,

];
