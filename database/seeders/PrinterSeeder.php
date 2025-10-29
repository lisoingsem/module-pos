<?php

declare(strict_types=1);

namespace Modules\POS\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\POS\Models\Printer;
use Modules\POS\Models\Terminal;

final class PrinterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $terminal = Terminal::first();

        if ( ! $terminal) {
            $this->command->info('No terminals found. Please seed terminals first.');

            return;
        }

        // Receipt printer (default)
        Printer::factory()
            ->receipt()
            ->default()
            ->create([
                'terminal_id' => $terminal->id,
                'ip_address' => '192.168.1.100',
            ]);

        // Kitchen printer
        Printer::factory()
            ->kitchen()
            ->create([
                'terminal_id' => $terminal->id,
                'ip_address' => '192.168.1.101',
            ]);

        $this->command->info('Printers seeded successfully!');
    }
}
