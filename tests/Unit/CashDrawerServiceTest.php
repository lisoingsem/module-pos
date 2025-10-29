<?php

declare(strict_types=1);

use Modules\POS\Models\CashMovement;
use Modules\POS\Models\Shift;
use Modules\POS\Models\Terminal;
use Modules\POS\Services\CashDrawerService;

beforeEach(function (): void {
    $this->user = App\Models\User::factory()->create();
    $this->actingAs($this->user);
    $this->terminal = Terminal::factory()->active()->create();
    $this->shift = Shift::factory()->open()->create(['terminal_id' => $this->terminal->id]);
    $this->service = app(CashDrawerService::class);
});

test('cash drawer service can record cash in', function (): void {
    $movement = $this->service->recordCashIn(
        $this->shift,
        100.00,
        'Additional funds',
        'Extra change needed'
    );

    expect($movement)->toBeInstanceOf(CashMovement::class);
    expect($movement->type->value)->toBe('cash_in');
    expect($movement->amount)->toBe(100.00);
    expect($movement->reason)->toBe('Additional funds');
    expect($movement->performed_by)->toBe($this->user->id);
});

test('cash drawer service can record cash out', function (): void {
    $movement = $this->service->recordCashOut(
        $this->shift,
        50.00,
        'Supplier payment'
    );

    expect($movement)->toBeInstanceOf(CashMovement::class);
    expect($movement->type->value)->toBe('cash_out');
    expect($movement->amount)->toBe(50.00);
});

test('cash drawer service can record petty cash', function (): void {
    $movement = $this->service->recordPettyCash(
        $this->shift,
        25.00,
        'Office supplies'
    );

    expect($movement->type->value)->toBe('petty_cash');
    expect($movement->amount)->toBe(25.00);
});

test('cash drawer service can record bank deposit', function (): void {
    $movement = $this->service->recordBankDeposit(
        $this->shift,
        1000.00,
        'DEP-12345'
    );

    expect($movement->type->value)->toBe('bank_deposit');
    expect($movement->amount)->toBe(1000.00);
    expect($movement->reference)->toBe('DEP-12345');
});

test('cash drawer service can record adjustment', function (): void {
    $movement = $this->service->recordAdjustment(
        $this->shift,
        -10.00,
        'Cash shortage'
    );

    expect($movement->type->value)->toBe('adjustment');
    expect($movement->amount)->toBe(-10.00);
});

test('cash drawer service calculates drawer summary correctly', function (): void {
    $shift = Shift::factory()->create([
        'opening_cash' => 500.00,
        'total_sales' => 1000.00,
        'total_refunds' => 50.00,
    ]);

    CashMovement::factory()->cashIn()->create([
        'shift_id' => $shift->id,
        'amount' => 100.00,
    ]);
    CashMovement::factory()->cashOut()->create([
        'shift_id' => $shift->id,
        'amount' => 50.00,
    ]);

    $summary = $this->service->getDrawerSummary($shift);

    expect($summary['opening_cash'])->toBe(500.00);
    expect($summary['total_cash_in'])->toBe(100.00);
    expect($summary['total_cash_out'])->toBe(50.00);
    expect($summary['total_sales'])->toBe(1000.00);
    expect($summary['total_refunds'])->toBe(50.00);
    expect($summary['current_balance'])->toBe(1500.00); // 500 + 100 - 50 + 1000 - 50
    expect($summary['expected_balance'])->toBe(1450.00); // 500 + 1000 - 50
});

test('cannot record movement on closed shift', function (): void {
    $closedShift = Shift::factory()->closed()->create(['terminal_id' => $this->terminal->id]);

    expect(fn () => $this->service->recordCashIn($closedShift, 100.00, 'Test'))
        ->toThrow(Exception::class);
});
