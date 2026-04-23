<?php

namespace Tests\Unit;

use App\Models\Like;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_post_belongs_to_user(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(User::class, $post->user);
        $this->assertEquals($user->id, $post->user->id);
    }

    public function test_is_liked_by_returns_true_when_liked(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create();

        $post->likes()->create(['user_id' => $user->id]);

        $this->assertTrue($post->isLikedBy($user));
    }

    public function test_is_liked_by_returns_false_when_not_liked(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create();

        $this->assertFalse($post->isLikedBy($user));
    }

    public function test_is_liked_by_returns_false_for_null_user(): void
    {
        $post = Post::factory()->create();

        $this->assertFalse($post->isLikedBy(null));
    }

    public function test_likes_count_is_accurate(): void
    {
        $post  = Post::factory()->create();
        $users = User::factory(4)->create();

        foreach ($users as $user) {
            $post->likes()->create(['user_id' => $user->id]);
        }

        $this->assertEquals(4, $post->likesCount());
    }

    public function test_comments_count_includes_replies(): void
    {
        $post    = Post::factory()->create();
        $user    = User::factory()->create();

        $comment = $post->allComments()->create([
            'user_id' => $user->id,
            'body'    => 'Top-level comment',
        ]);

        $post->allComments()->create([
            'user_id'   => $user->id,
            'parent_id' => $comment->id,
            'body'      => 'A reply',
        ]);

        $this->assertEquals(2, $post->commentsCount());
    }

    public function test_attachments_are_deleted_with_post(): void
    {
        $post       = Post::factory()->create();
        $attachment = $post->attachments()->create([
            'filename'  => 'test.jpg',
            'path'      => 'attachments/test.jpg',
            'disk'      => 'public',
            'mime_type' => 'image/jpeg',
            'size'      => 1024,
            'type'      => 'image',
        ]);

        $post->delete();

        $this->assertDatabaseMissing('attachments', ['id' => $attachment->id]);
    }
}
