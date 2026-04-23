<?php

namespace Tests\Unit;

use App\Models\Follow;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_avatar_url_returns_fallback_when_no_avatar(): void
    {
        $user = User::factory()->make(['avatar' => null]);

        $url = $user->avatarUrl();

        $this->assertStringContainsString('ui-avatars.com', $url);
        $this->assertStringContainsString(urlencode($user->name), $url);
    }

    public function test_avatar_url_returns_storage_url_when_set(): void
    {
        $user = User::factory()->make(['avatar' => 'avatars/test.jpg']);

        $url = $user->avatarUrl();

        $this->assertStringContainsString('avatars/test.jpg', $url);
    }

    public function test_route_key_is_username(): void
    {
        $user = new User();
        $this->assertEquals('username', $user->getRouteKeyName());
    }

    public function test_is_following_returns_true_when_following(): void
    {
        $follower  = User::factory()->create();
        $following = User::factory()->create();

        $follower->following()->attach($following->id);

        $this->assertTrue($follower->isFollowing($following));
    }

    public function test_is_following_returns_false_when_not_following(): void
    {
        $a = User::factory()->create();
        $b = User::factory()->create();

        $this->assertFalse($a->isFollowing($b));
    }

    public function test_followers_count_is_accurate(): void
    {
        $user      = User::factory()->create();
        $followers = User::factory(3)->create();

        foreach ($followers as $f) {
            $f->following()->attach($user->id);
        }

        $this->assertEquals(3, $user->followersCount());
    }

    public function test_following_count_is_accurate(): void
    {
        $user    = User::factory()->create();
        $targets = User::factory(5)->create();

        foreach ($targets as $t) {
            $user->following()->attach($t->id);
        }

        $this->assertEquals(5, $user->followingCount());
    }
}
