<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LikeTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_like_a_post(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create();

        $response = $this->actingAs($user)
            ->postJson("/posts/{$post->id}/like");

        $response->assertOk()
                 ->assertJson(['liked' => true, 'count' => 1]);

        $this->assertDatabaseHas('likes', [
            'user_id'       => $user->id,
            'likeable_type' => Post::class,
            'likeable_id'   => $post->id,
        ]);
    }

    public function test_user_can_unlike_a_post(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create();

        // Like it first
        $post->likes()->create(['user_id' => $user->id]);

        // Then unlike
        $response = $this->actingAs($user)
            ->postJson("/posts/{$post->id}/like");

        $response->assertOk()
                 ->assertJson(['liked' => false, 'count' => 0]);

        $this->assertDatabaseMissing('likes', [
            'user_id'     => $user->id,
            'likeable_id' => $post->id,
        ]);
    }

    public function test_guest_cannot_like(): void
    {
        $post = Post::factory()->create();

        $this->postJson("/posts/{$post->id}/like")
             ->assertUnauthorized();
    }
}
