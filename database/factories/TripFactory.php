<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Trip>
 */
class TripFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startLocation = fake()->streetAddress() . ', ' . fake()->city() . ', ' . fake()->stateAbbr();
        $endLocation = fake()->streetAddress() . ', ' . fake()->city() . ', ' . fake()->stateAbbr();
        
        return [
            'user_id' => User::factory(),
            'start_location' => $startLocation,
            'end_location' => $endLocation,
            'mileage' => fake()->randomFloat(1, 0.5, 500),
            'notes' => fake()->optional()->sentence(),
            'purpose' => fake()->randomElement([
                'Client meeting',
                'Sales call',
                'Business conference',
                'Office visit',
                'Supplier meeting',
                'Site inspection',
                'Training session',
                'Networking event'
            ]),
            'trip_date' => fake()->dateTimeBetween('-1 year', 'now')->format('Y-m-d'),
            'trip_time' => fake()->time(),
            'start_latitude' => fake()->latitude(),
            'start_longitude' => fake()->longitude(),
            'end_latitude' => fake()->latitude(),
            'end_longitude' => fake()->longitude(),
        ];
    }

    /**
     * Create a trip for the current year.
     */
    public function currentYear(): static
    {
        return $this->state(fn (array $attributes) => [
            'trip_date' => fake()->dateTimeBetween('2024-01-01', '2024-12-31')->format('Y-m-d'),
        ]);
    }

    /**
     * Create a trip for a specific month.
     */
    public function forMonth(int $year, int $month): static
    {
        $startDate = "{$year}-{$month}-01";
        $endDate = date('Y-m-t', strtotime($startDate));
        
        return $this->state(fn (array $attributes) => [
            'trip_date' => fake()->dateTimeBetween($startDate, $endDate)->format('Y-m-d'),
        ]);
    }

    /**
     * Create a high mileage trip.
     */
    public function highMileage(): static
    {
        return $this->state(fn (array $attributes) => [
            'mileage' => fake()->randomFloat(1, 100, 500),
        ]);
    }

    /**
     * Create a short trip.
     */
    public function shortTrip(): static
    {
        return $this->state(fn (array $attributes) => [
            'mileage' => fake()->randomFloat(1, 0.5, 10),
        ]);
    }
}
