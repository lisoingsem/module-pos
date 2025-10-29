<?php

declare(strict_types=1);

namespace Modules\POS\Services;

use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Modules\POS\Contracts\ShiftContract;
use Modules\POS\Enums\ShiftStatusEnum;
use Modules\POS\Models\Shift;

final class ShiftService
{
    /**
     * Create a new service instance.
     */
    public function __construct(
        private readonly ShiftContract $repository
    ) {}

    /**
     * Open a new shift.
     */
    public function openShift(int $terminalId, float $openingCash, ?string $notes = null): Shift
    {
        // Check if there's already an open shift for this terminal
        $existingShift = $this->repository->getOpenShiftByTerminal($terminalId);

        if ($existingShift) {
            throw new Exception(__('pos::shift.shift-already-open'));
        }

        return $this->repository->create([
            'terminal_id' => $terminalId,
            'status' => ShiftStatusEnum::OPEN->value,
            'opening_cash' => $openingCash,
            'opened_at' => now(),
            'opened_by' => Auth::id(),
            'notes' => $notes,
            'total_sales' => 0,
            'total_refunds' => 0,
            'total_transactions' => 0,
        ]);
    }

    /**
     * Close a shift.
     */
    public function closeShift(Shift $shift, float $actualCash, ?string $notes = null): Shift
    {
        if ( ! $shift->canBeClosed()) {
            throw new Exception(__('pos::shift.shift-cannot-be-closed'));
        }

        $expectedCash = $shift->calculateExpectedCash();
        $difference = $actualCash - $expectedCash;

        $this->repository->update($shift, [
            'status' => ShiftStatusEnum::CLOSED->value,
            'closing_cash' => $actualCash,
            'expected_cash' => $expectedCash,
            'actual_cash' => $actualCash,
            'difference' => $difference,
            'closed_at' => now(),
            'closed_by' => Auth::id(),
            'notes' => $notes ? ($shift->notes ? $shift->notes . "\n\n" . $notes : $notes) : $shift->notes,
        ]);

        return $shift->fresh();
    }

    /**
     * Suspend a shift.
     */
    public function suspendShift(Shift $shift, ?string $notes = null): Shift
    {
        if ( ! $shift->isOpen()) {
            throw new Exception(__('pos::shift.shift-must-be-open'));
        }

        $this->repository->update($shift, [
            'status' => ShiftStatusEnum::SUSPENDED->value,
            'notes' => $notes ? ($shift->notes ? $shift->notes . "\n\n" . $notes : $notes) : $shift->notes,
        ]);

        return $shift->fresh();
    }

    /**
     * Resume a shift.
     */
    public function resumeShift(Shift $shift): Shift
    {
        if ( ! $shift->isSuspended()) {
            throw new Exception(__('pos::shift.shift-must-be-suspended'));
        }

        $this->repository->update($shift, [
            'status' => ShiftStatusEnum::OPEN->value,
        ]);

        return $shift->fresh();
    }

    /**
     * Get open shift for a terminal.
     */
    public function getOpenShift(int $terminalId): ?Shift
    {
        return $this->repository->getOpenShiftByTerminal($terminalId);
    }

    /**
     * Get recent shifts for a terminal.
     */
    public function getRecentShifts(int $terminalId, int $limit = 10): Collection
    {
        return $this->repository->getRecentShiftsByTerminal($terminalId, $limit);
    }

    /**
     * Update shift sales totals (called when sales are made).
     */
    public function updateShiftSales(Shift $shift, float $amount, bool $isRefund = false): Shift
    {
        if ($isRefund) {
            $this->repository->update($shift, [
                'total_refunds' => $shift->total_refunds + $amount,
                'total_transactions' => $shift->total_transactions + 1,
            ]);
        } else {
            $this->repository->update($shift, [
                'total_sales' => $shift->total_sales + $amount,
                'total_transactions' => $shift->total_transactions + 1,
            ]);
        }

        return $shift->fresh();
    }
}
