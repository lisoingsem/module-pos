<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\POS\Http\Controllers\POSController;

Route::middleware(['auth', 'verified'])->group(function (): void {
    Route::resource('pos', POSController::class)->names('pos');
});
