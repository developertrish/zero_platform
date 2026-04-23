<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FollowTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_follow_another_user(): void
    {
        $follower  = User::factory()->create();
        $following = User::factory()->create();

        $this->actingAs($follower)
            ->post("/users/{$following->username}/follow")
            ->assertRedirect();

        $this->assertTrue($follower->fresh()->isFollowing($following));
    }

    public function test_user_can_unfollow(): void
    {
        $follower  = User::factory()->create();
        $following = User::factory()->create();

        $follower->following()->attach($following->id);

        $this->actingAs($follower)
            ->post("/users/{$following->username}/follow")
            ->assertRedirect();

        $this->assertFalse($follower->fresh()->isFollowing($following));
    }

    public function test_user_cannot_follow_themselves(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post("/users/{$user->username}/follow")
            ->assertRedirect();

        $this->assertFalse($user->fresh()->isFollowing($user));
    }

    public function test_followed_users_posts_appear_in_feed(): void
    {
        $user    = User::factory()->create();
        $friend  = User::factory()->create();
        $stranger = User::factory()->create();

        $user->following()->attach($friend->id);

        $friendPost   = \App\Models\Post::factory()->create(['user_id' => $friend->id]);
        $strangerPost = \App\Models\Post::factory()->create(['user_id' => $stranger->id]);

        $response = $this->actingAs($user)->get('/');

        $response->assertInertia(function ($page) use ($friendPost, $strangerPost) {
            $ids = collect($page->toArray()['props']['posts']['data'])->pluck('id');
            $this->assertTrue($ids->contains($friendPost->id));
            $this->assertFalse($ids->contains($strangerPost->id));
        });
    }
}
