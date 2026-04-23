<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_page_is_publicly_viewable(): void
    {
        $user = User::factory()->create();

        $this->get("/@{$user->username}")
             ->assertStatus(200)
             ->assertInertia(fn ($p) =>
                 $p->component('Profile/Show')
                   ->where('user.username', $user->username)
             );
    }

    public function test_edit_profile_page_requires_auth(): void
    {
        $this->get('/settings/profile')->assertRedirect('/login');
    }

    public function test_user_can_update_profile(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post('/settings/profile', [
                '_method'  => 'PATCH',
                'name'     => 'Updated Name',
                'username' => $user->username,
                'bio'      => 'My new bio',
                'location' => 'Cape Town',
                'website'  => 'https://example.com',
            ])
            ->assertRedirect("/@{$user->username}");

        $this->assertDatabaseHas('users', [
            'id'       => $user->id,
            'name'     => 'Updated Name',
            'bio'      => 'My new bio',
            'location' => 'Cape Town',
        ]);
    }

    public function test_user_can_upload_avatar(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $file = UploadedFile::fake()->image('avatar.jpg', 200, 200);

        $this->actingAs($user)
            ->post('/settings/profile', [
                '_method'  => 'PATCH',
                'name'     => $user->name,
                'username' => $user->username,
                'avatar'   => $file,
            ])
            ->assertRedirect();

        $user->refresh();
        $this->assertNotNull($user->avatar);
        Storage::disk('public')->assertExists($user->avatar);
    }

    public function test_username_must_be_alphanumeric(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post('/settings/profile', [
                '_method'  => 'PATCH',
                'name'     => $user->name,
                'username' => 'invalid username!',
            ])
            ->assertSessionHasErrors('username');
    }
}
