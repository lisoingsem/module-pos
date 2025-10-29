<?php

declare(strict_types=1);

use Modules\POS\Models\CashMovement;
use Modules\POS\Models\Shift;
use Modules\POS\Models\Terminal;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;

beforeEach(function (): void {
    $this->user = App\Models\User::factory()->create();
    $this->terminal = Terminal::factory()->active()->create();
    $this->shift = Shift::factory()->open()->create(['terminal_id' => $this->terminal->id]);
});

test('user can record cash in movement', function (): void {
    actingAs($this->user)
        ->post(route('dashboard.pos.cash-movements.store'), [
            'shift_id' => $this->shift->id,
            'type' => 'cash_in',
            'amount' => 100.00,
            'reason' => 'Additional funds',
            'notes' => 'Extra change needed',
        ])
        ->assertRedirect(route('dashboard.pos.cash-movements.index'));

    assertDatabaseHas('pos_cash_movements', [
        'shift_id' => $this->shift->id,
        'type' => 'cash_in',
        'amount' => 100.00,
        'reason' => 'Additional funds',
    ]);
});

test('user can record cash out movement', function (): void {
    actingAs($this->user)
        ->post(route('dashboard.pos.cash-movements.store'), [
            'shift_id' => $this->shift->id,
            'type' => 'cash_out',
            'amount' => 50.00,
            'reason' => 'Supplier payment',
        ])
        ->assertRedirect(route('dashboard.pos.cash-movements.index'));

    assertDatabaseHas('pos_cash_movements', [
        'shift_id' => $this->shift->id,
        'type' => 'cash_out',
        'amount' => 50.00,
    ]);
});

test('user can record petty cash movement', function (): void {
    actingAs($this->user)
        ->post(route('dashboard.pos.cash-movements.store'), [
            'shift_id' => $this->shift->id,
            'type' => 'petty_cash',
            'amount' => 25.00,
            'reason' => 'Office supplies',
        ])
        ->assertRedirect();

    assertDatabaseHas('pos_cash_movements', [
        'type' => 'petty_cash',
        'amount' => 25.00,
    ]);
});

test('cannot record movement without reason', function (): void {
    actingAs($this->user)
        ->post(route('dashboard.pos.cash-movements.store'), [
            'shift_id' => $this->shift->id,
            'type' => 'cash_in',
            'amount' => 100.00,
        ])
        ->assertSessionHasErrors('reason');
});

test('amount must be positive', function (): void {
    actingAs($this->user)
        ->post(route('dashboard.pos.cash-movements.store'), [
            'shift_id' => $this->shift->id,
            'type' => 'cash_in',
            'amount' => -100.00,
            'reason' => 'Test',
        ])
        ->assertSessionHasErrors('amount');
});

test('cash movement tracks performed by user', function (): void {
    CashMovement::factory()->create([
        'shift_id' => $this->shift->id,
        'performed_by' => $this->user->id,
    ]);

    $movement = CashMovement::first();

    expect($movement->performedBy->id)->toBe($this->user->id);
});

test('can get total cash in for a shift', function (): void {
    CashMovement::factory()->cashIn()->create([
        'shift_id' => $this->shift->id,
        'amount' => 100.00,
    ]);
    CashMovement::factory()->cashIn()->create([
        'shift_id' => $this->shift->id,
        'amount' => 50.00,
    ]);

    $repository = app(Modules\POS\Contracts\CashMovementContract::class);
    $totalCashIn = $repository->getTotalCashIn($this->shift->id);

    expect($totalCashIn)->toBe(150.00);
});

test('can get total cash out for a shift', function (): void {
    CashMovement::factory()->cashOut()->create([
        'shift_id' => $this->shift->id,
        'amount' => 30.00,
    ]);
    CashMovement::factory()->cashOut()->create([
        'shift_id' => $this->shift->id,
        'amount' => 20.00,
    ]);

    $repository = app(Modules\POS\Contracts\CashMovementContract::class);
    $totalCashOut = $repository->getTotalCashOut($this->shift->id);

    expect($totalCashOut)->toBe(50.00);
});

test('cash movement helper methods work correctly', function (): void {
    $cashIn = CashMovement::factory()->cashIn()->create();
    $cashOut = CashMovement::factory()->cashOut()->create();

    expect($cashIn->isCashIn())->toBeTrue();
    expect($cashOut->isCashOut())->toBeTrue();
});
