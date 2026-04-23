<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PostFactory extends Factory
{
    public function definition(): array
    {
        $samples = [
            "Just wrapped up a long week. Time to decompress and do absolutely nothing productive.",
            "Tried making sourdough bread again. Still not great. Still very edible.",
            "The best part of working from home is the commute.",
            "Read three books this month. Feeling suspiciously well-read.",
            "Hot take: a solid walk fixes most things.",
            "There's something deeply satisfying about a clean desk. Mine will remain a fantasy.",
            "Coffee count today: more than I'll admit publicly.",
            "Started learning something new. Asked myself why. No answer yet.",
            "Spent the evening rearranging my bookshelves. This is what peak adulthood looks like.",
            "Finally got around to fixing that thing I said I'd fix six months ago. Growth.",
        ];

        return [
            'user_id'    => User::factory(),
            'body'       => fake()->boolean(30)
                ? $samples[array_rand($samples)]
                : fake()->paragraphs(rand(1, 3), true),
            'created_at' => fake()->dateTimeBetween('-6 months', 'now'),
            'updated_at' => fn(array $attrs) => $attrs['created_at'],
        ];
    }
}
