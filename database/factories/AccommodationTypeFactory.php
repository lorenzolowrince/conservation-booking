<?php

namespace Database\Factories;

use App\Models\AccommodationType;
use App\Models\ConservationArea;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AccommodationType>
 */
class AccommodationTypeFactory extends Factory
{
    public function definition(): array
    {
        return [
            'conservation_area_id' => ConservationArea::factory(),
            'name' => fake()->words(2, true) . ' Chalet',
            'type' => 'chalet',
            'description' => fake()->sentence(),
            'capacity' => 2,
            'total_units' => 5,
            'price_per_night' => fake()->randomFloat(2, 100, 500),
            'price_per_night_foreigner' => null,
            'is_active' => true,
        ];
    }
}
