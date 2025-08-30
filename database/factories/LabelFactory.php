<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Label>
 */
class LabelFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $labels = [
            'client', 'meeting', 'sales', 'conference', 'training',
            'office', 'supplier', 'networking', 'emergency', 'routine',
            'travel', 'presentation', 'inspection', 'consultation', 'delivery'
        ];

        $colors = [
            '#FF6B6B', '#4ECDC4', '#45B7D1', '#96CEB4', '#FECA57',
            '#FF9FF3', '#54A0FF', '#5F27CD', '#00D2D3', '#FF9F43',
            '#10AC84', '#EE5A24', '#0ABDE3', '#C44569', '#FD79A8'
        ];

        return [
            'user_id' => User::factory(),
            'name' => fake()->unique()->randomElement($labels),
            'color' => fake()->randomElement($colors),
        ];
    }

    /**
     * Create a label with a specific name.
     */
    public function withName(string $name): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => $name,
        ]);
    }

    /**
     * Create a label with a specific color.
     */
    public function withColor(string $color): static
    {
        return $this->state(fn (array $attributes) => [
            'color' => $color,
        ]);
    }
}
