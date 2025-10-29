<?php

declare(strict_types=1);

namespace Modules\POS\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\POS\Models\Terminal;

final class TerminalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $terminals = [
            [
                'name' => 'Main Counter Terminal',
                'code' => 'TERM-MAIN-01',
                'location' => 'Main Store - Counter 1',
                'status' => 'active',
            ],
            [
                'name' => 'Express Checkout Terminal',
                'code' => 'TERM-EXPRESS-01',
                'location' => 'Main Store - Express Lane',
                'status' => 'active',
            ],
            [
                'name' => 'Backup Terminal',
                'code' => 'TERM-BACKUP-01',
                'location' => 'Main Store - Back Office',
                'status' => 'inactive',
            ],
        ];

        foreach ($terminals as $terminalData) {
            Terminal::updateOrCreate(
                ['code' => $terminalData['code']],
                $terminalData
            );
        }
    }
}
