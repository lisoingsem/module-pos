<?php

declare(strict_types=1);

namespace Modules\POS\Services;

use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Modules\POS\Contracts\CashMovementContract;
use Modules\POS\Enums\CashMovementTypeEnum;
use Modules\POS\Models\CashMovement;
use Modules\POS\Models\Shift;

final class CashDrawerService
{
    /**
     * Create a new service instance.
     */
    public function __construct(
        private readonly CashMovementContract $repository
    ) {}

    /**
     * Record cash in movement.
     */
    public function recordCashIn(
        Shift $shift,
        float $amount,
        string $reason,
        ?string $notes = null,
        ?string $reference = null
    ): CashMovement {
        if ( ! $shift->isOpen()) {
            throw new Exception(__('pos::cash.shift-must-be-open'));
        }

        return $this->repository->create([
            'shift_id' => $shift->id,
            'type' => CashMovementTypeEnum::CASH_IN->value,
            'amount' => $amount,
            'reason' => $reason,
            'notes' => $notes,
            'reference' => $reference,
            'performed_by' => Auth::id(),
        ]);
    }

    /**
     * Record cash out movement.
     */
    public function recordCashOut(
        Shift $shift,
        float $amount,
        string $reason,
        ?string $notes = null,
        ?string $reference = null
    ): CashMovement {
        if ( ! $shift->isOpen()) {
            throw new Exception(__('pos::cash.shift-must-be-open'));
        }

        return $this->repository->create([
            'shift_id' => $shift->id,
            'type' => CashMovementTypeEnum::CASH_OUT->value,
            'amount' => $amount,
            'reason' => $reason,
            'notes' => $notes,
            'reference' => $reference,
            'performed_by' => Auth::id(),
        ]);
    }

    /**
     * Record petty cash withdrawal.
     */
    public function recordPettyCash(
        Shift $shift,
        float $amount,
        string $reason,
        ?string $notes = null
    ): CashMovement {
        if ( ! $shift->isOpen()) {
            throw new Exception(__('pos::cash.shift-must-be-open'));
        }

        return $this->repository->create([
            'shift_id' => $shift->id,
            'type' => CashMovementTypeEnum::PETTY_CASH->value,
            'amount' => $amount,
            'reason' => $reason,
            'notes' => $notes,
            'performed_by' => Auth::id(),
        ]);
    }

    /**
     * Record bank deposit.
     */
    public function recordBankDeposit(
        Shift $shift,
        float $amount,
        ?string $reference = null,
        ?string $notes = null
    ): CashMovement {
        return $this->repository->create([
            'shift_id' => $shift->id,
            'type' => CashMovementTypeEnum::BANK_DEPOSIT->value,
            'amount' => $amount,
            'reason' => 'Bank deposit',
            'reference' => $reference,
            'notes' => $notes,
            'performed_by' => Auth::id(),
        ]);
    }

    /**
     * Record cash adjustment.
     */
    public function recordAdjustment(
        Shift $shift,
        float $amount,
        string $reason,
        ?string $notes = null
    ): CashMovement {
        if ( ! $shift->isOpen()) {
            throw new Exception(__('pos::cash.shift-must-be-open'));
        }

        return $this->repository->create([
            'shift_id' => $shift->id,
            'type' => CashMovementTypeEnum::ADJUSTMENT->value,
            'amount' => $amount,
            'reason' => $reason,
            'notes' => $notes,
            'performed_by' => Auth::id(),
        ]);
    }

    /**
     * Get all movements for a shift.
     */
    public function getShiftMovements(int $shiftId): Collection
    {
        return $this->repository->getByShift($shiftId);
    }

    /**
     * Get cash drawer summary for a shift.
     */
    public function getDrawerSummary(Shift $shift): array
    {
        $totalCashIn = $this->repository->getTotalCashIn($shift->id);
        $totalCashOut = $this->repository->getTotalCashOut($shift->id);
        $currentBalance = $shift->opening_cash + $totalCashIn - $totalCashOut + $shift->total_sales - $shift->total_refunds;

        return [
            'opening_cash' => $shift->opening_cash,
            'total_cash_in' => $totalCashIn,
            'total_cash_out' => $totalCashOut,
            'total_sales' => $shift->total_sales,
            'total_refunds' => $shift->total_refunds,
            'current_balance' => $currentBalance,
            'expected_balance' => $shift->calculateExpectedCash(),
        ];
    }
}
