<?php

declare(strict_types=1);

namespace Modules\POS\Http\Controllers\Inertia\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Modules\POS\Contracts\TerminalContract;
use Modules\POS\Http\Requests\Terminal\StoreRequest;
use Modules\POS\Http\Requests\Terminal\UpdateRequest;
use Modules\POS\Http\Resources\TerminalResource;
use Modules\POS\Models\Terminal;
use Modules\POS\Services\TerminalService;

final class TerminalController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct(
        private readonly TerminalService $service,
        private readonly TerminalContract $repository
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        $terminals = $this->repository->builder()
            ->with('terminalable')
            ->withCount(['shifts', 'printers'])
            ->when(
                $request->input('search'),
                fn ($q, $search) => $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhere('location', 'like', "%{$search}%")
            )
            ->when(
                $request->input('status'),
                fn ($q, $status) => $q->where('status', $status)
            )
            ->latest()
            ->paginate(15);

        $items = TerminalResource::collection($terminals)->response()->getData(true);

        return Inertia::render('POS::Dashboard/Terminals/Index', [
            'items' => $items,
            'title' => __('pos::terminal.manage-terminals'),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        $item = [
            'name' => null,
            'code' => null,
            'location' => null,
            'status' => 'active',
            'terminalable_type' => null,
            'terminalable_id' => null,
            'settings' => null,
        ];

        return Inertia::render('POS::Dashboard/Terminals/Create', [
            'title' => __('pos::terminal.create-terminal'),
            'item' => $item,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRequest $request): RedirectResponse
    {
        $this->service->create($request->validated());

        return redirect()->route('dashboard.pos.terminals.index')
            ->with('success', __('pos::terminal.created-successfully'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Terminal $terminal): Response
    {
        $terminal->load(['terminalable', 'shifts', 'printers']);

        return Inertia::render('POS::Dashboard/Terminals/Show', [
            'title' => __('pos::terminal.terminal-details'),
            'item' => new TerminalResource($terminal),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Terminal $terminal): Response
    {
        return Inertia::render('POS::Dashboard/Terminals/Edit', [
            'title' => __('pos::terminal.edit-terminal'),
            'item' => new TerminalResource($terminal),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRequest $request, Terminal $terminal): RedirectResponse
    {
        $this->service->update($terminal, $request->validated());

        return redirect()->route('dashboard.pos.terminals.index')
            ->with('success', __('pos::terminal.updated-successfully'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Terminal $terminal): RedirectResponse
    {
        $this->service->delete($terminal);

        return redirect()->route('dashboard.pos.terminals.index')
            ->with('success', __('pos::terminal.deleted-successfully'));
    }
}
