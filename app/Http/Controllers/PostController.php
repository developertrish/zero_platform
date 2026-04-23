<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePostRequest;
use App\Models\Attachment;
use App\Models\Post;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class PostController extends Controller
{
    /**
     * Chronological feed — no algorithm, ever.
     * Posts from followed users + self, newest first.
     */
    public function index(Request $request): Response
    {
        $user = Auth::user();
        $ids  = $user->following()->pluck('users.id')->push($user->id);

        $posts = Post::with(['user', 'attachments', 'likes', 'comments.user', 'comments.likes', 'comments.replies.user', 'comments.replies.likes'])
            ->whereIn('user_id', $ids)
            ->orderBy('created_at', 'desc')   // strict chronological — no score ever
            ->paginate(20)
            ->through(fn($p) => $this->serializePost($p));

        $suggestions = \App\Models\User::whereNotIn('id', $ids)
            ->inRandomOrder()
            ->limit(5)
            ->get()
            ->map(fn($u) => [
                'id'         => $u->id,
                'name'       => $u->name,
                'username'   => $u->username,
                'avatar_url' => $u->avatarUrl(),
            ]);

        return Inertia::render('Posts/Feed', compact('posts', 'suggestions'));
    }

    /** Global explore — all posts, chronological */
    public function explore(): Response
    {
        $posts = Post::with(['user', 'attachments', 'likes', 'comments.user', 'comments.likes', 'comments.replies.user', 'comments.replies.likes'])
            ->orderBy('created_at', 'desc')
            ->paginate(20)
            ->through(fn($p) => $this->serializePost($p));

        return Inertia::render('Posts/Explore', compact('posts'));
    }

    public function show(Post $post): Response
    {
        $post->load(['user', 'attachments', 'likes', 'comments.user', 'comments.likes', 'comments.replies.user', 'comments.replies.likes']);

        return Inertia::render('Posts/Show', [
            'post' => $this->serializePost($post),
        ]);
    }

    public function store(StorePostRequest $request): RedirectResponse
    {
        $post = Auth::user()->posts()->create(['body' => $request->validated('body')]);

        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('attachments', 'public');
                $post->attachments()->create([
                    'filename'  => $file->getClientOriginalName(),
                    'path'      => $path,
                    'disk'      => 'public',
                    'mime_type' => $file->getMimeType(),
                    'size'      => $file->getSize(),
                    'type'      => Attachment::typeFromMime($file->getMimeType()),
                ]);
            }
        }

        return redirect()->route('feed')->with('success', 'Post published.');
    }

    public function destroy(Post $post): RedirectResponse
    {
        $this->authorize('delete', $post);

        foreach ($post->attachments as $att) {
            Storage::disk('public')->delete($att->path);
        }

        $post->delete();

        return back()->with('success', 'Post deleted.');
    }

    // ─── Serializer ───────────────────────────────────────────────────────────

    private function serializePost(Post $post): array
    {
        $authUser = Auth::user();

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
            'comments' => $post->comments
                ->whereNull('parent_id')
                ->values()
                ->map(fn($c) => $this->serializeComment($c)),
        ];
    }

    private function serializeComment(\App\Models\Comment $c): array
    {
        $authUser = Auth::user();

        return [
            'id'            => $c->id,
            'body'          => $c->body,
            'created_at'    => $c->created_at->toIso8601String(),
            'user_id'       => $c->user_id,
            'likes_count'   => $c->likes->count(),
            'liked_by_auth' => $authUser ? $c->likes->contains('user_id', $authUser->id) : false,
            'user'          => [
                'id'         => $c->user->id,
                'name'       => $c->user->name,
                'username'   => $c->user->username,
                'avatar_url' => $c->user->avatarUrl(),
            ],
            'replies' => ($c->replies ?? collect())->map(fn($r) => $this->serializeComment($r))->values(),
        ];
    }
}
