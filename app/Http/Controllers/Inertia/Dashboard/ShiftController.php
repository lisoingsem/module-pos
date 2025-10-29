<?php

declare(strict_types=1);

namespace Modules\POS\Http\Controllers\Inertia\Dashboard;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Modules\POS\Contracts\ShiftContract;
use Modules\POS\Contracts\TerminalContract;
use Modules\POS\Http\Requests\Shift\CloseShiftRequest;
use Modules\POS\Http\Requests\Shift\OpenShiftRequest;
use Modules\POS\Http\Resources\ShiftResource;
use Modules\POS\Http\Resources\TerminalResource;
use Modules\POS\Models\Shift;
use Modules\POS\Services\ShiftService;

final class ShiftController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct(
        private readonly ShiftService $service,
        private readonly ShiftContract $repository,
        private readonly TerminalContract $terminalRepository
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        $shifts = $this->repository->builder()
            ->with(['terminal', 'openedBy', 'closedBy'])
            ->withCount('cashMovements')
            ->when(
                $request->input('search'),
                fn ($q, $search) => $q->whereHas('terminal', fn ($q2) => $q2->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('openedBy', fn ($q2) => $q2->where('name', 'like', "%{$search}%"))
            )
            ->when(
                $request->input('status'),
                fn ($q, $status) => $q->where('status', $status)
            )
            ->when(
                $request->input('terminal_id'),
                fn ($q, $terminalId) => $q->where('terminal_id', $terminalId)
            )
            ->latest('opened_at')
            ->paginate(15);

        $items = ShiftResource::collection($shifts)->response()->getData(true);

        // Get active terminals for filtering
        $activeTerminals = $this->terminalRepository->builder()
            ->active()
            ->orderBy('name')
            ->get();

        return Inertia::render('POS::Dashboard/Shifts/Index', [
            'items' => $items,
            'title' => __('pos::shift.manage-shifts'),
            'terminals' => TerminalResource::collection($activeTerminals),
        ]);
    }

    /**
     * Show the form for opening a new shift.
     */
    public function create(): Response
    {
        $activeTerminals = $this->terminalRepository->builder()
            ->active()
            ->orderBy('name')
            ->get();

        return Inertia::render('POS::Dashboard/Shifts/Open', [
            'title' => __('pos::shift.open-shift'),
            'terminals' => TerminalResource::collection($activeTerminals),
        ]);
    }

    /**
     * Open a new shift.
     */
    public function store(OpenShiftRequest $request): RedirectResponse
    {
        try {
            $this->service->openShift(
                $request->validated('terminal_id'),
                $request->validated('opening_cash'),
                $request->validated('notes')
            );

            return redirect()->route('dashboard.pos.shifts.index')
                ->with('success', __('pos::shift.opened-successfully'));
        } catch (Exception $e) {
            return redirect()->back()
                ->with('error', $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Shift $shift): Response
    {
        $shift->load(['terminal', 'openedBy', 'closedBy', 'cashMovements']);

        return Inertia::render('POS::Dashboard/Shifts/Show', [
            'title' => __('pos::shift.shift-details'),
            'item' => new ShiftResource($shift),
        ]);
    }

    /**
     * Close a shift.
     */
    public function close(CloseShiftRequest $request, Shift $shift): RedirectResponse
    {
        try {
            $this->service->closeShift(
                $shift,
                $request->validated('actual_cash'),
                $request->validated('notes')
            );

            return redirect()->route('dashboard.pos.shifts.index')
                ->with('success', __('pos::shift.closed-successfully'));
        } catch (Exception $e) {
            return redirect()->back()
                ->with('error', $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Suspend a shift.
     */
    public function suspend(Request $request, Shift $shift): RedirectResponse
    {
        try {
            $this->service->suspendShift($shift, $request->input('notes'));

            return redirect()->route('dashboard.pos.shifts.index')
                ->with('success', __('pos::shift.suspended-successfully'));
        } catch (Exception $e) {
            return redirect()->back()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Resume a shift.
     */
    public function resume(Shift $shift): RedirectResponse
    {
        try {
            $this->service->resumeShift($shift);

            return redirect()->route('dashboard.pos.shifts.index')
                ->with('success', __('pos::shift.resumed-successfully'));
        } catch (Exception $e) {
            return redirect()->back()
                ->with('error', $e->getMessage());
        }
    }
}
