<?php

declare(strict_types=1);

use Modules\POS\Models\Shift;
use Modules\POS\Models\Terminal;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;

beforeEach(function (): void {
    $this->user = App\Models\User::factory()->create();
    $this->terminal = Terminal::factory()->active()->create();
});

test('user can open a shift', function (): void {
    actingAs($this->user)
        ->post(route('dashboard.pos.shifts.store'), [
            'terminal_id' => $this->terminal->id,
            'opening_cash' => 500.00,
            'notes' => 'Morning shift',
        ])
        ->assertRedirect(route('dashboard.pos.shifts.index'));

    assertDatabaseHas('pos_shifts', [
        'terminal_id' => $this->terminal->id,
        'opening_cash' => 500.00,
        'status' => 'open',
    ]);
});

test('cannot open duplicate shift for same terminal', function (): void {
    Shift::factory()->open()->create(['terminal_id' => $this->terminal->id]);

    actingAs($this->user)
        ->post(route('dashboard.pos.shifts.store'), [
            'terminal_id' => $this->terminal->id,
            'opening_cash' => 500.00,
        ])
        ->assertSessionHasErrors();
});

test('user can close a shift', function (): void {
    $shift = Shift::factory()->open()->create([
        'terminal_id' => $this->terminal->id,
        'opening_cash' => 500.00,
        'total_sales' => 1000.00,
        'total_refunds' => 50.00,
    ]);

    $actualCash = 1450.00; // opening + sales - refunds

    actingAs($this->user)
        ->post(route('dashboard.pos.shifts.close', $shift->uuid), [
            'actual_cash' => $actualCash,
            'notes' => 'End of shift',
        ])
        ->assertRedirect(route('dashboard.pos.shifts.index'));

    $shift->refresh();

    expect($shift->status->value)->toBe('closed');
    expect($shift->actual_cash)->toBe(1450.00);
    expect($shift->difference)->toBe(0.00);
});

test('shift calculates expected cash correctly', function (): void {
    $shift = Shift::factory()->create([
        'opening_cash' => 500.00,
        'total_sales' => 1000.00,
        'total_refunds' => 50.00,
    ]);

    $expectedCash = $shift->calculateExpectedCash();

    expect($expectedCash)->toBe(1450.00); // 500 + 1000 - 50
});

test('shift tracks cash difference correctly', function (): void {
    $shift = Shift::factory()->open()->create([
        'opening_cash' => 500.00,
        'total_sales' => 1000.00,
        'total_refunds' => 50.00,
    ]);

    $actualCash = 1440.00; // $10 short

    actingAs($this->user)
        ->post(route('dashboard.pos.shifts.close', $shift->uuid), [
            'actual_cash' => $actualCash,
        ])
        ->assertRedirect();

    $shift->refresh();

    expect($shift->difference)->toBe(-10.00);
});

test('user can suspend a shift', function (): void {
    $shift = Shift::factory()->open()->create(['terminal_id' => $this->terminal->id]);

    actingAs($this->user)
        ->post(route('dashboard.pos.shifts.suspend', $shift->uuid), [
            'notes' => 'Lunch break',
        ])
        ->assertRedirect(route('dashboard.pos.shifts.index'));

    $shift->refresh();

    expect($shift->status->value)->toBe('suspended');
});

test('user can resume a suspended shift', function (): void {
    $shift = Shift::factory()->suspended()->create(['terminal_id' => $this->terminal->id]);

    actingAs($this->user)
        ->post(route('dashboard.pos.shifts.resume', $shift->uuid))
        ->assertRedirect(route('dashboard.pos.shifts.index'));

    $shift->refresh();

    expect($shift->status->value)->toBe('open');
});

test('shift helper methods work correctly', function (): void {
    $openShift = Shift::factory()->open()->create();
    $closedShift = Shift::factory()->closed()->create();
    $suspendedShift = Shift::factory()->suspended()->create();

    expect($openShift->isOpen())->toBeTrue();
    expect($closedShift->isClosed())->toBeTrue();
    expect($suspendedShift->isSuspended())->toBeTrue();

    expect($openShift->canBeClosed())->toBeTrue();
    expect($suspendedShift->canBeClosed())->toBeTrue();
    expect($closedShift->canBeClosed())->toBeFalse();
});
