<?php

declare(strict_types=1);

namespace Modules\POS\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\POS\Models\Shift;
use Modules\POS\Models\Terminal;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Modules\POS\Models\Shift>
 */
final class ShiftFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = Shift::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        $openedAt = fake()->dateTimeBetween('-7 days', '-1 day');
        $openingCash = fake()->randomFloat(2, 100, 1000);
        $totalSales = fake()->randomFloat(2, 500, 5000);
        $totalRefunds = fake()->randomFloat(2, 0, 500);

        return [
            'terminal_id' => Terminal::factory(),
            'status' => fake()->randomElement(['open', 'closed']),
            'opening_cash' => $openingCash,
            'total_sales' => $totalSales,
            'total_refunds' => $totalRefunds,
            'total_transactions' => fake()->numberBetween(10, 100),
            'opened_at' => $openedAt,
            'opened_by' => 1, // Assumes user ID 1 exists
        ];
    }

    /**
     * Indicate that the shift is open.
     */
    public function open(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'open',
            'closed_at' => null,
            'closing_cash' => null,
            'expected_cash' => null,
            'actual_cash' => null,
            'difference' => null,
            'closed_by' => null,
        ]);
    }

    /**
     * Indicate that the shift is closed.
     */
    public function closed(): static
    {
        return $this->state(function (array $attributes) {
            $openedAt = $attributes['opened_at'] ?? fake()->dateTimeBetween('-7 days', '-1 day');
            $closedAt = fake()->dateTimeBetween($openedAt, 'now');
            $openingCash = $attributes['opening_cash'] ?? fake()->randomFloat(2, 100, 1000);
            $totalSales = $attributes['total_sales'] ?? fake()->randomFloat(2, 500, 5000);
            $totalRefunds = $attributes['total_refunds'] ?? fake()->randomFloat(2, 0, 500);
            $expectedCash = $openingCash + $totalSales - $totalRefunds;
            $actualCash = $expectedCash + fake()->randomFloat(2, -50, 50);

            return [
                'status' => 'closed',
                'closed_at' => $closedAt,
                'expected_cash' => $expectedCash,
                'actual_cash' => $actualCash,
                'difference' => $actualCash - $expectedCash,
                'closing_cash' => $actualCash,
                'closed_by' => 1,
            ];
        });
    }

    /**
     * Indicate that the shift is suspended.
     */
    public function suspended(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'suspended',
            'closed_at' => null,
            'closing_cash' => null,
            'expected_cash' => null,
            'actual_cash' => null,
            'difference' => null,
            'closed_by' => null,
        ]);
    }
}
