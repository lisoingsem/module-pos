<?php

declare(strict_types=1);

namespace Modules\POS\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\POS\Models\Printer;
use Modules\POS\Models\Terminal;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Modules\POS\Models\Printer>
 */
final class PrinterFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = Printer::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        $type = fake()->randomElement(['receipt', 'kitchen', 'label', 'report']);

        return [
            'terminal_id' => Terminal::factory(),
            'name' => fake()->words(2, true) . ' Printer',
            'type' => $type,
            'connection_type' => fake()->randomElement(['network', 'usb', 'bluetooth']),
            'ip_address' => fake()->localIpv4(),
            'port' => 9100,
            'paper_width' => fake()->randomElement([58, 80]),
            'is_default' => false,
            'is_active' => true,
        ];
    }

    /**
     * Receipt printer.
     */
    public function receipt(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'receipt',
            'name' => 'Receipt Printer',
        ]);
    }

    /**
     * Kitchen printer.
     */
    public function kitchen(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'kitchen',
            'name' => 'Kitchen Printer',
        ]);
    }

    /**
     * Default printer.
     */
    public function default(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_default' => true,
        ]);
    }
}
