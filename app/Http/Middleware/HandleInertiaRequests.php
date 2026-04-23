<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Defines the props that are shared by default.
     * Available in every Vue page as `$page.props`.
     */
    public function share(Request $request): array
    {
        return array_merge(parent::share($request), [
            'auth' => [
                'user' => $request->user() ? [
                    'id'            => $request->user()->id,
                    'name'          => $request->user()->name,
                    'username'      => $request->user()->username,
                    'email'         => $request->user()->email,
                    'avatar_url'    => $request->user()->avatarUrl(),
                    'bio'           => $request->user()->bio,
                    'location'      => $request->user()->location,
                    'website'       => $request->user()->website,
                ] : null,
            ],

            // Flash messages — picked up by AppLayout's toast watcher
            'flash' => [
                'success' => $request->session()->get('success'),
                'error'   => $request->session()->get('error'),
            ],
        ]);
    }
}
