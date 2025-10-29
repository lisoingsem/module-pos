<?php

declare(strict_types=1);

use Modules\POS\Models\Terminal;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;

beforeEach(function (): void {
    $this->user = App\Models\User::factory()->create();
});

test('user can view terminals index page', function (): void {
    Terminal::factory()->count(3)->create();

    actingAs($this->user)
        ->get(route('dashboard.pos.terminals.index'))
        ->assertOk();
});

test('user can create a terminal', function (): void {
    $terminalData = [
        'name' => 'Test Terminal',
        'code' => 'TEST-001',
        'location' => 'Main Store',
        'status' => 'active',
    ];

    actingAs($this->user)
        ->post(route('dashboard.pos.terminals.store'), $terminalData)
        ->assertRedirect(route('dashboard.pos.terminals.index'));

    assertDatabaseHas('pos_terminals', [
        'name' => 'Test Terminal',
        'code' => 'TEST-001',
        'status' => 'active',
    ]);
});

test('terminal code must be unique', function (): void {
    Terminal::factory()->create(['code' => 'DUPLICATE-001']);

    actingAs($this->user)
        ->post(route('dashboard.pos.terminals.store'), [
            'name' => 'Another Terminal',
            'code' => 'DUPLICATE-001',
            'status' => 'active',
        ])
        ->assertSessionHasErrors('code');
});

test('user can update a terminal', function (): void {
    $terminal = Terminal::factory()->create([
        'name' => 'Old Name',
        'status' => 'inactive',
    ]);

    actingAs($this->user)
        ->put(route('dashboard.pos.terminals.update', $terminal->uuid), [
            'name' => 'Updated Name',
            'code' => $terminal->code,
            'status' => 'active',
        ])
        ->assertRedirect(route('dashboard.pos.terminals.index'));

    assertDatabaseHas('pos_terminals', [
        'id' => $terminal->id,
        'name' => 'Updated Name',
        'status' => 'active',
    ]);
});

test('user can delete a terminal', function (): void {
    $terminal = Terminal::factory()->create();

    actingAs($this->user)
        ->delete(route('dashboard.pos.terminals.destroy', $terminal->uuid))
        ->assertRedirect(route('dashboard.pos.terminals.index'));

    assertDatabaseMissing('pos_terminals', [
        'id' => $terminal->id,
        'deleted_at' => null,
    ]);
});

test('terminal can be filtered by status', function (): void {
    Terminal::factory()->active()->count(2)->create();
    Terminal::factory()->inactive()->count(3)->create();

    $activeTerminals = Terminal::active()->get();

    expect($activeTerminals)->toHaveCount(2);
});

test('terminal status helper methods work correctly', function (): void {
    $activeTerminal = Terminal::factory()->active()->create();
    $inactiveTerminal = Terminal::factory()->inactive()->create();
    $maintenanceTerminal = Terminal::factory()->maintenance()->create();

    expect($activeTerminal->isActive())->toBeTrue();
    expect($inactiveTerminal->isInactive())->toBeTrue();
    expect($maintenanceTerminal->isUnderMaintenance())->toBeTrue();
});
