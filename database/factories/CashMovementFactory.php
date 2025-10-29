<?php

declare(strict_types=1);

namespace Modules\POS\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\POS\Models\CashMovement;
use Modules\POS\Models\Shift;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Modules\POS\Models\CashMovement>
 */
final class CashMovementFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = CashMovement::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        $type = fake()->randomElement(['cash_in', 'cash_out', 'petty_cash', 'bank_deposit']);

        return [
            'shift_id' => Shift::factory(),
            'type' => $type,
            'amount' => fake()->randomFloat(2, 10, 500),
            'payment_method' => fake()->optional()->randomElement(['cash', 'card', 'mobile_money', 'bank_transfer']),
            'reason' => $this->getReasonForType($type),
            'notes' => fake()->optional()->sentence(),
            'reference' => fake()->optional()->numerify('REF-#####'),
            'performed_by' => 1, // Assumes user ID 1 exists
        ];
    }

    /**
     * Cash in movement.
     */
    public function cashIn(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'cash_in',
            'reason' => fake()->randomElement(['Cash sale', 'Customer payment', 'Additional funds']),
        ]);
    }

    /**
     * Cash out movement.
     */
    public function cashOut(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'cash_out',
            'reason' => fake()->randomElement(['Supplier payment', 'Expense', 'Withdrawal']),
        ]);
    }

    /**
     * Petty cash movement.
     */
    public function pettyCash(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'petty_cash',
            'amount' => fake()->randomFloat(2, 5, 100),
            'reason' => fake()->randomElement(['Office supplies', 'Refreshments', 'Misc expense']),
        ]);
    }

    /**
     * Bank deposit movement.
     */
    public function bankDeposit(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'bank_deposit',
            'amount' => fake()->randomFloat(2, 500, 5000),
            'reason' => 'Bank deposit',
            'reference' => fake()->numerify('DEP-#####'),
        ]);
    }

    /**
     * Get reason based on type.
     */
    private function getReasonForType(string $type): string
    {
        return match ($type) {
            'cash_in' => fake()->randomElement(['Cash sale', 'Customer payment', 'Additional funds']),
            'cash_out' => fake()->randomElement(['Supplier payment', 'Expense', 'Withdrawal']),
            'petty_cash' => fake()->randomElement(['Office supplies', 'Refreshments', 'Misc expense']),
            'bank_deposit' => 'Bank deposit',
            default => 'Cash movement',
        };
    }
}
