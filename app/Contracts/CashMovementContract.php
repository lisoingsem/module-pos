<?php

declare(strict_types=1);

namespace Modules\POS\Contracts;

use App\Contracts\BaseEloquentContract;
use Illuminate\Database\Eloquent\Collection;

interface CashMovementContract extends BaseEloquentContract
{
    /**
     * Get cash movements for a shift.
     */
    public function getByShift(int $shiftId): Collection;

    /**
     * Get total cash in for a shift.
     */
    public function getTotalCashIn(int $shiftId): float;

    /**
     * Get total cash out for a shift.
     */
    public function getTotalCashOut(int $shiftId): float;
}
