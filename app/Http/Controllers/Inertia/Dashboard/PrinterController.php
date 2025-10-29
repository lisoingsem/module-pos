<?php

declare(strict_types=1);

namespace Modules\POS\Http\Controllers\Inertia\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Modules\POS\Contracts\PrinterContract;
use Modules\POS\Http\Requests\Printer\StoreRequest;
use Modules\POS\Http\Requests\Printer\UpdateRequest;
use Modules\POS\Http\Resources\PrinterResource;
use Modules\POS\Models\Printer;
use Modules\POS\Services\PrinterService;

final class PrinterController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct(
        private readonly PrinterService $service,
        private readonly PrinterContract $repository
    ) {}

    /**
     * Display a listing of printers.
     */
    public function index(Request $request): Response
    {
        $printers = $this->repository->builder()
            ->with('terminal')
            ->when(
                $request->input('search'),
                fn ($q, $search) => $q->where('name', 'like', "%{$search}%")
                    ->orWhere('ip_address', 'like', "%{$search}%")
            )
            ->when(
                $request->input('type'),
                fn ($q, $type) => $q->where('type', $type)
            )
            ->when(
                $request->input('terminal_id'),
                fn ($q, $terminalId) => $q->where('terminal_id', $terminalId)
            )
            ->latest()
            ->paginate(15);

        $items = PrinterResource::collection($printers)->response()->getData(true);

        return Inertia::render('POS::Dashboard/Printers/Index', [
            'items' => $items,
            'title' => __('pos::printer.manage-printers'),
        ]);
    }

    /**
     * Show the form for creating a new printer.
     */
    public function create(): Response
    {
        return Inertia::render('POS::Dashboard/Printers/Create', [
            'title' => __('pos::printer.create-printer'),
        ]);
    }

    /**
     * Store a newly created printer.
     */
    public function store(StoreRequest $request): RedirectResponse
    {
        $printer = $this->repository->create($request->validated());

        return redirect()->route('dashboard.pos.printers.index')
            ->with('success', __('pos::printer.created-successfully'));
    }

    /**
     * Display the specified printer.
     */
    public function show(Printer $printer): Response
    {
        $printer->load('terminal');

        return Inertia::render('POS::Dashboard/Printers/Show', [
            'title' => __('pos::printer.printer-details'),
            'item' => new PrinterResource($printer),
        ]);
    }

    /**
     * Show the form for editing the specified printer.
     */
    public function edit(Printer $printer): Response
    {
        return Inertia::render('POS::Dashboard/Printers/Edit', [
            'title' => __('pos::printer.edit-printer'),
            'item' => new PrinterResource($printer),
        ]);
    }

    /**
     * Update the specified printer.
     */
    public function update(UpdateRequest $request, Printer $printer): RedirectResponse
    {
        $this->repository->update($printer, $request->validated());

        return redirect()->route('dashboard.pos.printers.index')
            ->with('success', __('pos::printer.updated-successfully'));
    }

    /**
     * Remove the specified printer.
     */
    public function destroy(Printer $printer): RedirectResponse
    {
        $this->repository->delete($printer);

        return redirect()->route('dashboard.pos.printers.index')
            ->with('success', __('pos::printer.deleted-successfully'));
    }

    /**
     * Set printer as default.
     */
    public function setDefault(Printer $printer): RedirectResponse
    {
        $this->service->setAsDefault($printer);

        return redirect()->back()
            ->with('success', __('pos::printer.set-as-default-successfully'));
    }
}
