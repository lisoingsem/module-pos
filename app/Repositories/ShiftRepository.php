<?php

declare(strict_types=1);

namespace Modules\POS\Repositories;

use App\Repositories\BaseEloquentRepository;
use Illuminate\Database\Eloquent\Collection;
use Modules\POS\Contracts\ShiftContract;
use Modules\POS\Models\Shift;

final class ShiftRepository extends BaseEloquentRepository implements ShiftContract
{
    /**
     * Create a new instance of the repository.
     */
    public function __construct()
    {
        $this->model = new Shift();
    }

    /**
     * Get open shift for a terminal.
     */
    public function getOpenShiftByTerminal(int $terminalId): ?Shift
    {
        return $this->model
            ->where('terminal_id', $terminalId)
            ->open()
            ->with(['terminal', 'openedBy'])
            ->first();
    }

    /**
     * Get recent shifts for a terminal.
     */
    public function getRecentShiftsByTerminal(int $terminalId, int $limit = 10): Collection
    {
        return $this->model
            ->where('terminal_id', $terminalId)
            ->with(['terminal', 'openedBy', 'closedBy'])
            ->latest('opened_at')
            ->limit($limit)
            ->get();
    }
}
