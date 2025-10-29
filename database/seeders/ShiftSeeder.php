<?php

declare(strict_types=1);

namespace Modules\POS\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\POS\Models\Shift;
use Modules\POS\Models\Terminal;

final class ShiftSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $terminal = Terminal::where('code', 'TERM-MAIN-01')->first();

        if ( ! $terminal) {
            $this->command->info('No terminals found. Please seed terminals first.');

            return;
        }

        // Create a closed shift
        Shift::factory()
            ->for($terminal)
            ->closed()
            ->create([
                'opened_at' => now()->subHours(8),
                'closed_at' => now()->subHours(1),
            ]);

        // Create an open shift
        Shift::factory()
            ->for($terminal)
            ->open()
            ->create([
                'opened_at' => now()->subHours(2),
            ]);

        $this->command->info('Shifts seeded successfully!');
    }
}
