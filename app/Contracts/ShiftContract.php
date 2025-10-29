<?php

declare(strict_types=1);

namespace Modules\POS\Contracts;

use App\Contracts\BaseEloquentContract;
use Illuminate\Database\Eloquent\Collection;
use Modules\POS\Models\Shift;

interface ShiftContract extends BaseEloquentContract
{
    /**
     * Get open shift for a terminal.
     */
    public function getOpenShiftByTerminal(int $terminalId): ?Shift;

    /**
     * Get recent shifts for a terminal.
     */
    public function getRecentShiftsByTerminal(int $terminalId, int $limit = 10): Collection;
}
