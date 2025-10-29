<?php

declare(strict_types=1);

namespace Modules\POS\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\POS\Models\Terminal;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Modules\POS\Models\Terminal>
 */
final class TerminalFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = Terminal::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'name' => fake()->words(2, true) . ' Terminal',
            'code' => 'TERM-' . fake()->unique()->numberBetween(1000, 9999),
            'location' => fake()->optional()->city(),
            'status' => fake()->randomElement(['active', 'inactive', 'maintenance']),
            'settings' => null,
        ];
    }

    /**
     * Indicate that the terminal is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'active',
        ]);
    }

    /**
     * Indicate that the terminal is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'inactive',
        ]);
    }

    /**
     * Indicate that the terminal is under maintenance.
     */
    public function maintenance(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'maintenance',
        ]);
    }
}
