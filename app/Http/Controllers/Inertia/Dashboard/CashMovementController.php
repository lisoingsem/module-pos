<?php

declare(strict_types=1);

namespace Modules\POS\Http\Controllers\Inertia\Dashboard;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Modules\POS\Contracts\CashMovementContract;
use Modules\POS\Http\Requests\CashMovement\RecordMovementRequest;
use Modules\POS\Http\Resources\CashMovementResource;
use Modules\POS\Models\CashMovement;
use Modules\POS\Models\Shift;
use Modules\POS\Services\CashDrawerService;

final class CashMovementController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct(
        private readonly CashDrawerService $service,
        private readonly CashMovementContract $repository
    ) {}

    /**
     * Display a listing of cash movements.
     */
    public function index(Request $request): Response
    {
        $movements = $this->repository->builder()
            ->with(['shift.terminal', 'performedBy'])
            ->when(
                $request->input('search'),
                fn ($q, $search) => $q->where('reason', 'like', "%{$search}%")
                    ->orWhere('reference', 'like', "%{$search}%")
            )
            ->when(
                $request->input('type'),
                fn ($q, $type) => $q->where('type', $type)
            )
            ->when(
                $request->input('shift_id'),
                fn ($q, $shiftId) => $q->where('shift_id', $shiftId)
            )
            ->latest()
            ->paginate(15);

        $items = CashMovementResource::collection($movements)->response()->getData(true);

        return Inertia::render('POS::Dashboard/CashMovements/Index', [
            'items' => $items,
            'title' => __('pos::cash.manage-movements'),
        ]);
    }

    /**
     * Show the form for recording a new cash movement.
     */
    public function create(): Response
    {
        $openShifts = Shift::open()->with('terminal')->get();

        return Inertia::render('POS::Dashboard/CashMovements/Create', [
            'title' => __('pos::cash.record-movement'),
            'shifts' => $openShifts,
        ]);
    }

    /**
     * Store a newly created cash movement.
     */
    public function store(RecordMovementRequest $request): RedirectResponse
    {
        try {
            $shift = Shift::findOrFail($request->validated('shift_id'));
            $type = $request->validated('type');
            $amount = $request->validated('amount');
            $reason = $request->validated('reason');
            $notes = $request->validated('notes');
            $reference = $request->validated('reference');

            match ($type) {
                'cash_in' => $this->service->recordCashIn($shift, $amount, $reason, $notes, $reference),
                'cash_out' => $this->service->recordCashOut($shift, $amount, $reason, $notes, $reference),
                'petty_cash' => $this->service->recordPettyCash($shift, $amount, $reason, $notes),
                'bank_deposit' => $this->service->recordBankDeposit($shift, $amount, $reference, $notes),
                'adjustment' => $this->service->recordAdjustment($shift, $amount, $reason, $notes),
                default => throw new Exception(__('pos::cash.invalid-type')),
            };

            return redirect()->route('dashboard.pos.cash-movements.index')
                ->with('success', __('pos::cash.recorded-successfully'));
        } catch (Exception $e) {
            return redirect()->back()
                ->with('error', $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified cash movement.
     */
    public function show(CashMovement $cashMovement): Response
    {
        $cashMovement->load(['shift.terminal', 'performedBy']);

        return Inertia::render('POS::Dashboard/CashMovements/Show', [
            'title' => __('pos::cash.movement-details'),
            'item' => new CashMovementResource($cashMovement),
        ]);
    }

    /**
     * Get cash drawer summary for a shift.
     */
    public function drawerSummary(Shift $shift): Response
    {
        $summary = $this->service->getDrawerSummary($shift);
        $movements = $this->service->getShiftMovements($shift->id);

        return Inertia::render('POS::Dashboard/CashMovements/DrawerSummary', [
            'title' => __('pos::cash.drawer-summary'),
            'summary' => $summary,
            'movements' => CashMovementResource::collection($movements),
            'shift' => $shift,
        ]);
    }
}
