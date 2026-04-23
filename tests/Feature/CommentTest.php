<?php

namespace Tests\Feature;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommentTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_comment_on_a_post(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create();

        $response = $this->actingAs($user)
            ->postJson("/posts/{$post->id}/comments", [
                'body' => 'Great post!',
            ]);

        $response->assertCreated()
                 ->assertJsonPath('comment.body', 'Great post!')
                 ->assertJsonPath('comment.user.id', $user->id);

        $this->assertDatabaseHas('comments', [
            'post_id' => $post->id,
            'user_id' => $user->id,
            'body'    => 'Great post!',
        ]);
    }

    public function test_user_can_reply_to_a_comment(): void
    {
        $user    = User::factory()->create();
        $post    = Post::factory()->create();
        $comment = Comment::factory()->create(['post_id' => $post->id]);

        $response = $this->actingAs($user)
            ->postJson("/posts/{$post->id}/comments", [
                'body'      => 'Nice reply!',
                'parent_id' => $comment->id,
            ]);

        $response->assertCreated();

        $this->assertDatabaseHas('comments', [
            'parent_id' => $comment->id,
            'body'      => 'Nice reply!',
        ]);
    }

    public function test_user_can_delete_own_comment(): void
    {
        $user    = User::factory()->create();
        $comment = Comment::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user)
            ->deleteJson("/comments/{$comment->id}")
            ->assertOk()
            ->assertJson(['deleted' => true]);

        $this->assertDatabaseMissing('comments', ['id' => $comment->id]);
    }

    public function test_post_author_can_delete_any_comment(): void
    {
        $postAuthor = User::factory()->create();
        $commenter  = User::factory()->create();
        $post       = Post::factory()->create(['user_id' => $postAuthor->id]);
        $comment    = Comment::factory()->create([
            'post_id' => $post->id,
            'user_id' => $commenter->id,
        ]);

        $this->actingAs($postAuthor)
            ->deleteJson("/comments/{$comment->id}")
            ->assertOk();

        $this->assertDatabaseMissing('comments', ['id' => $comment->id]);
    }

    public function test_other_user_cannot_delete_comment(): void
    {
        $owner   = User::factory()->create();
        $other   = User::factory()->create();
        $comment = Comment::factory()->create(['user_id' => $owner->id]);

        $this->actingAs($other)
            ->deleteJson("/comments/{$comment->id}")
            ->assertForbidden();
    }

    public function test_comment_body_is_required(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create();

        $this->actingAs($user)
            ->postJson("/posts/{$post->id}/comments", ['body' => ''])
            ->assertUnprocessable();
    }
}
