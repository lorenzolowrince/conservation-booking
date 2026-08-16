<?php

namespace Database\Factories;

use App\Models\ConservationArea;
use App\Models\Package;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Package>
 */
class PackageFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->words(3, true) . ' Package';

        return [
            'conservation_area_id' => ConservationArea::factory(),
            'name' => $name,
            'slug' => Str::slug($name) . '-' . fake()->unique()->numberBetween(1, 100000),
            'description' => fake()->sentence(),
            'duration_days' => fake()->numberBetween(1, 5),
            'min_pax' => 1,
            'max_pax' => 10,
            'daily_capacity' => null,
            'price_per_person' => fake()->randomFloat(2, 100, 1000),
            'price_per_person_foreigner' => null,
            'is_active' => true,
            'sort_order' => 0,
        ];
    }
}
