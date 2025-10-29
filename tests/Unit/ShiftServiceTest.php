<?php

declare(strict_types=1);

use Modules\POS\Models\Shift;
use Modules\POS\Models\Terminal;
use Modules\POS\Services\ShiftService;

beforeEach(function (): void {
    $this->user = App\Models\User::factory()->create();
    $this->actingAs($this->user);
    $this->terminal = Terminal::factory()->active()->create();
    $this->service = app(ShiftService::class);
});

test('shift service can open a new shift', function (): void {
    $shift = $this->service->openShift(
        $this->terminal->id,
        500.00,
        'Morning shift'
    );

    expect($shift)->toBeInstanceOf(Shift::class);
    expect($shift->terminal_id)->toBe($this->terminal->id);
    expect($shift->opening_cash)->toBe(500.00);
    expect($shift->status->value)->toBe('open');
    expect($shift->opened_by)->toBe($this->user->id);
});

test('shift service prevents duplicate open shifts', function (): void {
    $this->service->openShift($this->terminal->id, 500.00);

    expect(fn () => $this->service->openShift($this->terminal->id, 500.00))
        ->toThrow(Exception::class);
});

test('shift service can close a shift', function (): void {
    $shift = Shift::factory()->open()->create([
        'terminal_id' => $this->terminal->id,
        'opening_cash' => 500.00,
        'total_sales' => 1000.00,
        'total_refunds' => 50.00,
    ]);

    $closedShift = $this->service->closeShift($shift, 1450.00);

    expect($closedShift->status->value)->toBe('closed');
    expect($closedShift->actual_cash)->toBe(1450.00);
    expect($closedShift->difference)->toBe(0.00);
    expect($closedShift->closed_by)->toBe($this->user->id);
});

test('shift service can suspend a shift', function (): void {
    $shift = Shift::factory()->open()->create(['terminal_id' => $this->terminal->id]);

    $suspendedShift = $this->service->suspendShift($shift, 'Lunch break');

    expect($suspendedShift->status->value)->toBe('suspended');
});

test('shift service can resume a shift', function (): void {
    $shift = Shift::factory()->suspended()->create(['terminal_id' => $this->terminal->id]);

    $resumedShift = $this->service->resumeShift($shift);

    expect($resumedShift->status->value)->toBe('open');
});

test('shift service can get open shift for terminal', function (): void {
    Shift::factory()->open()->create(['terminal_id' => $this->terminal->id]);

    $openShift = $this->service->getOpenShift($this->terminal->id);

    expect($openShift)->toBeInstanceOf(Shift::class);
    expect($openShift->status->value)->toBe('open');
});

test('shift service updates shift sales correctly', function (): void {
    $shift = Shift::factory()->open()->create([
        'terminal_id' => $this->terminal->id,
        'total_sales' => 1000.00,
        'total_transactions' => 10,
    ]);

    $updatedShift = $this->service->updateShiftSales($shift, 150.00, false);

    expect($updatedShift->total_sales)->toBe(1150.00);
    expect($updatedShift->total_transactions)->toBe(11);
});

test('shift service updates refunds correctly', function (): void {
    $shift = Shift::factory()->open()->create([
        'terminal_id' => $this->terminal->id,
        'total_refunds' => 50.00,
        'total_transactions' => 10,
    ]);

    $updatedShift = $this->service->updateShiftSales($shift, 25.00, true);

    expect($updatedShift->total_refunds)->toBe(75.00);
    expect($updatedShift->total_transactions)->toBe(11);
});
