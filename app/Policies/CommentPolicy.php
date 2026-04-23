<?php

namespace App\Policies;

use App\Models\Comment;
use App\Models\User;

class CommentPolicy
{
    /**
     * Users can delete their own comments.
     * Post authors can delete any comment on their post.
     */
    public function delete(User $user, Comment $comment): bool
    {
        if ($user->id === $comment->user_id) {
            return true;
        }

        // Post author can moderate — avoid N+1 by using value() when relation not loaded
        $postUserId = $comment->relationLoaded('post')
            ? $comment->post->user_id
            : $comment->post()->value('user_id');

        return $user->id === $postUserId;
    }
}
