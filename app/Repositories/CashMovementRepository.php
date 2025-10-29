<?php

declare(strict_types=1);

namespace Modules\POS\Repositories;

use App\Repositories\BaseEloquentRepository;
use Illuminate\Database\Eloquent\Collection;
use Modules\POS\Contracts\CashMovementContract;
use Modules\POS\Models\CashMovement;

final class CashMovementRepository extends BaseEloquentRepository implements CashMovementContract
{
    /**
     * Create a new instance of the repository.
     */
    public function __construct()
    {
        $this->model = new CashMovement();
    }

    /**
     * Get cash movements for a shift.
     */
    public function getByShift(int $shiftId): Collection
    {
        return $this->model
            ->where('shift_id', $shiftId)
            ->with(['shift', 'performedBy'])
            ->latest()
            ->get();
    }

    /**
     * Get total cash in for a shift.
     */
    public function getTotalCashIn(int $shiftId): float
    {
        return (float) $this->model
            ->where('shift_id', $shiftId)
            ->cashIn()
            ->sum('amount');
    }

    /**
     * Get total cash out for a shift.
     */
    public function getTotalCashOut(int $shiftId): float
    {
        return (float) $this->model
            ->where('shift_id', $shiftId)
            ->cashOut()
            ->sum('amount');
    }
}
