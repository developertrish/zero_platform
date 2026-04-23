<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Server Side Rendering
    |--------------------------------------------------------------------------
    | Inertia SSR is disabled by default. Enable it by setting this to true
    | and running `php artisan inertia:start-ssr` with a Node.js SSR server.
    */

    'ssr' => [
        'enabled' => false,
        'url'     => 'http://127.0.0.1:13714',
    ],

    /*
    |--------------------------------------------------------------------------
    | Testing
    |--------------------------------------------------------------------------
    */

    'testing' => [
        'ensure_pages_exist' => true,
        'page_paths'         => [resource_path('js/Pages')],
        'page_extensions'    => ['vue', 'svelte', 'jsx', 'tsx'],
    ],

];
