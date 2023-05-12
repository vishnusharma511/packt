<?php

namespace Database\Factories;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Book>
 */
class BookFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'title' => fake()->name(),
            'author' => fake()->name(),
            'publication_date' => Carbon::now()->subDays(rand(0, 7))->format('Y-m-d'),
            'isbn'=> Str::random(15),
            'genre'=> fake()->name()
        ];
    }
}
