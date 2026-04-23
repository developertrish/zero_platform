<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateProfileRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    public function show(User $user): Response
    {
        $authUser = Auth::user();

        $posts = $user->posts()
            ->with(['user', 'attachments', 'likes', 'comments.user', 'comments.likes'])
            ->orderBy('created_at', 'desc')
            ->paginate(15)
            ->through(function ($post) use ($authUser) {
                return [
                    'id'             => $post->id,
                    'user_id'        => $post->user_id,
                    'body'           => $post->body,
                    'created_at'     => $post->created_at->toIso8601String(),
                    'likes_count'    => $post->likes->count(),
                    'comments_count' => $post->allComments()->count(),
                    'liked_by_auth'  => $authUser ? $post->likes->contains('user_id', $authUser->id) : false,
                    'user'           => [
                        'id'         => $post->user->id,
                        'name'       => $post->user->name,
                        'username'   => $post->user->username,
                        'avatar_url' => $post->user->avatarUrl(),
                    ],
                    'attachments' => $post->attachments->map(fn($a) => [
                        'id'        => $a->id,
                        'filename'  => $a->filename,
                        'url'       => $a->url(),
                        'type'      => $a->type,
                        'mime_type' => $a->mime_type,
                        'size'      => $a->size,
                    ]),
                    'comments' => [],
                ];
            });

        return Inertia::render('Profile/Show', [
            'user' => [
                'id'              => $user->id,
                'name'            => $user->name,
                'username'        => $user->username,
                'avatar_url'      => $user->avatarUrl(),
                'bio'             => $user->bio,
                'location'        => $user->location,
                'website'         => $user->website,
                'posts_count'     => $user->posts()->count(),
                'followers_count' => $user->followers()->count(),
                'following_count' => $user->following()->count(),
            ],
            'posts'       => $posts,
            'isFollowing' => $authUser ? $authUser->isFollowing($user) : false,
            'isOwn'       => $authUser?->id === $user->id,
        ]);
    }

    public function edit(): Response
    {
        $user = Auth::user();

        return Inertia::render('Profile/Edit', [
            'user' => [
                'id'         => $user->id,
                'name'       => $user->name,
                'username'   => $user->username,
                'avatar_url' => $user->avatarUrl(),
                'bio'        => $user->bio,
                'location'   => $user->location,
                'website'    => $user->website,
            ],
        ]);
    }

    public function update(UpdateProfileRequest $request): RedirectResponse
    {
        $user = Auth::user();
        $data = $request->safe()->except(['avatar', '_method']);

        if ($request->hasFile('avatar')) {
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $user->update($data);

        return redirect()->route('profile.show', $user->username)
            ->with('success', 'Profile updated.');
    }
}
