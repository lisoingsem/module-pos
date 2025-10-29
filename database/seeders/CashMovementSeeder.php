<?php

declare(strict_types=1);

namespace Modules\POS\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\POS\Models\CashMovement;
use Modules\POS\Models\Shift;

final class CashMovementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $openShift = Shift::where('status', 'open')->first();

        if ( ! $openShift) {
            $this->command->info('No open shifts found. Please seed shifts first.');

            return;
        }

        // Create various cash movements for the open shift
        CashMovement::factory()
            ->for($openShift, 'shift')
            ->cashIn()
            ->create([
                'amount' => 100.00,
                'reason' => 'Additional starting cash',
                'created_at' => $openShift->opened_at->addMinutes(15),
            ]);

        CashMovement::factory()
            ->for($openShift, 'shift')
            ->pettyCash()
            ->create([
                'amount' => 25.50,
                'reason' => 'Office supplies',
                'created_at' => $openShift->opened_at->addMinutes(45),
            ]);

        CashMovement::factory()
            ->for($openShift, 'shift')
            ->cashOut()
            ->create([
                'amount' => 150.00,
                'reason' => 'Supplier payment',
                'created_at' => $openShift->opened_at->addHour(),
            ]);

        $this->command->info('Cash movements seeded successfully!');
    }
}
