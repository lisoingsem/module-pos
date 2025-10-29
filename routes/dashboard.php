<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\POS\Http\Controllers\Inertia\Dashboard\CashierController;
use Modules\POS\Http\Controllers\Inertia\Dashboard\CashMovementController;
use Modules\POS\Http\Controllers\Inertia\Dashboard\PrinterController;
use Modules\POS\Http\Controllers\Inertia\Dashboard\ShiftController;
use Modules\POS\Http\Controllers\Inertia\Dashboard\TerminalController;

Route::prefix('pos')->name('pos.')->group(function (): void {
    // Cashier Interface
    Route::get('cashier', [CashierController::class, 'index'])->name('cashier.index');
    Route::post('cashier/cart', [CashierController::class, 'createCart'])->name('cashier.cart.create');
    Route::post('cashier/cart/{order}/add', [CashierController::class, 'addToCart'])->name('cashier.cart.add');
    Route::patch('cashier/cart/item/{itemId}', [CashierController::class, 'updateCartItem'])->name('cashier.cart.update');
    Route::delete('cashier/cart/item/{itemId}', [CashierController::class, 'removeFromCart'])->name('cashier.cart.remove');
    Route::post('cashier/checkout/{order}', [CashierController::class, 'checkout'])->name('cashier.checkout');
    Route::get('cashier/receipt/{order}', [CashierController::class, 'getReceipt'])->name('cashier.receipt');
    Route::post('cashier/receipt/{order}/print', [CashierController::class, 'printReceipt'])->name('cashier.receipt.print');
    Route::get('cashier/products/search', [CashierController::class, 'searchProducts'])->name('cashier.products.search');

    Route::resource('terminals', TerminalController::class);

    // Shifts
    Route::resource('shifts', ShiftController::class)->except(['edit', 'update', 'destroy']);
    Route::post('shifts/{shift}/close', [ShiftController::class, 'close'])->name('shifts.close');
    Route::post('shifts/{shift}/suspend', [ShiftController::class, 'suspend'])->name('shifts.suspend');
    Route::post('shifts/{shift}/resume', [ShiftController::class, 'resume'])->name('shifts.resume');

    // Cash Movements
    Route::resource('cash-movements', CashMovementController::class)->only(['index', 'create', 'store', 'show']);
    Route::get('cash-drawer/{shift}/summary', [CashMovementController::class, 'drawerSummary'])->name('cash-drawer.summary');

    // Printers
    Route::resource('printers', PrinterController::class);
    Route::post('printers/{printer}/set-default', [PrinterController::class, 'setDefault'])->name('printers.set-default');
});
