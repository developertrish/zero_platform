<?php

namespace Database\Factories;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CommentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'post_id'    => Post::factory(),
            'user_id'    => User::factory(),
            'parent_id'  => null,
            'body'       => fake()->sentences(rand(1, 3), true),
            'created_at' => fake()->dateTimeBetween('-3 months', 'now'),
            'updated_at' => fn(array $attrs) => $attrs['created_at'],
        ];
    }

    public function reply(int $parentId): static
    {
        return $this->state(fn() => ['parent_id' => $parentId]);
    }
}
