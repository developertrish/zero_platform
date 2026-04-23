<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_view_feed(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page->component('Posts/Feed'));
    }

    public function test_guest_is_redirected_from_feed(): void
    {
        $this->get('/')->assertRedirect('/login');
    }

    public function test_user_can_create_a_post(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/posts', [
            'body' => 'Hello, Chronicle!',
        ]);

        $response->assertRedirect('/');
        $this->assertDatabaseHas('posts', [
            'user_id' => $user->id,
            'body'    => 'Hello, Chronicle!',
        ]);
    }

    public function test_post_body_is_required(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post('/posts', ['body' => ''])
            ->assertSessionHasErrors('body');
    }

    public function test_user_can_delete_own_post(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user)
            ->delete("/posts/{$post->id}")
            ->assertRedirect();

        $this->assertDatabaseMissing('posts', ['id' => $post->id]);
    }

    public function test_user_cannot_delete_others_post(): void
    {
        $owner  = User::factory()->create();
        $other  = User::factory()->create();
        $post   = Post::factory()->create(['user_id' => $owner->id]);

        $this->actingAs($other)
            ->delete("/posts/{$post->id}")
            ->assertForbidden();

        $this->assertDatabaseHas('posts', ['id' => $post->id]);
    }

    public function test_feed_is_strictly_chronological(): void
    {
        $user = User::factory()->create();

        // Create posts out-of-order
        $older = Post::factory()->create([
            'user_id'    => $user->id,
            'created_at' => now()->subHours(2),
        ]);
        $newer = Post::factory()->create([
            'user_id'    => $user->id,
            'created_at' => now()->subHour(),
        ]);

        $response = $this->actingAs($user)->get('/');

        $response->assertInertia(fn ($page) =>
            $page->component('Posts/Feed')
                 ->where('posts.data.0.id', $newer->id)  // newest first
                 ->where('posts.data.1.id', $older->id)
        );
    }

    public function test_single_post_is_viewable(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user)
            ->get("/posts/{$post->id}")
            ->assertStatus(200)
            ->assertInertia(fn ($page) =>
                $page->component('Posts/Show')
                     ->where('post.id', $post->id)
            );
    }
}
