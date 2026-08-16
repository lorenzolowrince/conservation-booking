<?php

namespace Database\Factories;

use App\Models\Booking;
use App\Models\ConservationArea;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Booking>
 */
class BookingFactory extends Factory
{
    public function definition(): array
    {
        $checkIn = fake()->dateTimeBetween('+1 day', '+30 days');
        $checkOut = (clone $checkIn)->modify('+' . fake()->numberBetween(1, 5) . ' days');

        return [
            'booking_ref' => 'TEST-' . fake()->unique()->numberBetween(100000, 999999),
            'conservation_area_id' => ConservationArea::factory(),
            'package_id' => null,
            'accommodation_type_id' => null,
            'contact_name' => fake()->name(),
            'contact_email' => fake()->safeEmail(),
            'contact_phone' => fake()->phoneNumber(),
            'contact_nationality' => 'Malaysian',
            'booking_type' => 'package',
            'check_in_date' => $checkIn->format('Y-m-d'),
            'check_out_date' => $checkOut->format('Y-m-d'),
            'num_adults' => 2,
            'num_children' => 0,
            'subtotal' => 500,
            'tax' => 30,
            'total_amount' => 530,
            'status' => 'pending',
            'payment_status' => 'unpaid',
        ];
    }

    public function confirmed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'confirmed',
            'confirmed_at' => now(),
        ]);
    }

    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'cancelled',
            'cancelled_at' => now(),
        ]);
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
            'check_in_date' => now()->subDays(10)->format('Y-m-d'),
            'check_out_date' => now()->subDays(8)->format('Y-m-d'),
        ]);
    }
}
