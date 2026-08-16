<?php

namespace Database\Factories;

use App\Models\ConservationArea;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<ConservationArea>
 */
class ConservationAreaFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->unique()->city() . ' Conservation Area';

        return [
            'code' => strtoupper(fake()->unique()->lexify('????')),
            'name' => $name,
            'short_name' => $name,
            'slug' => Str::slug($name) . '-' . fake()->unique()->numberBetween(1, 100000),
            'description' => fake()->sentence(),
            'about' => fake()->paragraph(),
            'location' => fake()->city() . ', Sabah',
            'area_hectares' => fake()->numberBetween(1000, 50000),
            'difficulty_level' => fake()->randomElement(['easy', 'moderate', 'challenging']),
            'is_active' => true,
            'sort_order' => 0,
        ];
    }
}
