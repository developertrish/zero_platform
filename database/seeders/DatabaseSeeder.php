<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\Like;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Demo user
        $demo = User::factory()->create([
            'name'     => 'Demo User',
            'username' => 'demo',
            'email'    => 'demo@example.com',
            'password' => Hash::make('password'),
            'bio'      => 'Just a demo account exploring Chronicle — the algorithm-free social platform.',
            'location' => 'Internet',
        ]);

        // Create 15 other users
        $users = User::factory(15)->create();
        $all   = $users->push($demo);

        // Make some follow relationships
        foreach ($all as $user) {
            $toFollow = $all->filter(fn($u) => $u->id !== $user->id)->random(rand(3, 8));
            foreach ($toFollow as $target) {
                $user->following()->syncWithoutDetaching([$target->id]);
            }
        }

        // Each user creates some posts
        foreach ($all as $user) {
            $posts = Post::factory(rand(3, 10))->create(['user_id' => $user->id]);

            // Add some likes from random users
            foreach ($posts as $post) {
                $likers = $all->random(rand(0, 8));
                foreach ($likers as $liker) {
                    Like::firstOrCreate([
                        'user_id'       => $liker->id,
                        'likeable_type' => Post::class,
                        'likeable_id'   => $post->id,
                    ]);
                }

                // Add some comments
                $commenters = $all->random(rand(0, 5));
                foreach ($commenters as $commenter) {
                    $comment = Comment::create([
                        'post_id' => $post->id,
                        'user_id' => $commenter->id,
                        'body'    => fake()->sentences(rand(1, 3), true),
                    ]);

                    // Occasional reply
                    if (rand(0, 1)) {
                        $replier = $all->random();
                        Comment::create([
                            'post_id'   => $post->id,
                            'user_id'   => $replier->id,
                            'parent_id' => $comment->id,
                            'body'      => fake()->sentence(),
                        ]);
                    }
                }
            }
        }
    }
}
