<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCommentRequest;
use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function store(StoreCommentRequest $request, Post $post): JsonResponse
    {
        $comment = $post->allComments()->create([
            'user_id'   => Auth::id(),
            'parent_id' => $request->validated('parent_id'),
            'body'      => $request->validated('body'),
        ]);

        $comment->load('user', 'likes', 'replies');

        $user = $comment->user;

        return response()->json([
            'comment' => [
                'id'            => $comment->id,
                'body'          => $comment->body,
                'created_at'    => $comment->created_at->toIso8601String(),
                'user_id'       => $comment->user_id,
                'likes_count'   => 0,
                'liked_by_auth' => false,
                'user'          => [
                    'id'         => $user->id,
                    'name'       => $user->name,
                    'username'   => $user->username,
                    'avatar_url' => $user->avatarUrl(),
                ],
                'replies' => [],
            ],
        ], 201);
    }

    public function destroy(Comment $comment): JsonResponse|RedirectResponse
    {
        $this->authorize('delete', $comment);
        $comment->delete();

        if (request()->wantsJson()) {
            return response()->json(['deleted' => true]);
        }

        return back()->with('success', 'Comment deleted.');
    }
}
