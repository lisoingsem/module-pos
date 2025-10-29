<?php

declare(strict_types=1);

namespace Modules\POS\Services;

use Illuminate\Database\Eloquent\Collection;
use Modules\POS\Contracts\PrinterContract;
use Modules\POS\Models\Printer;

final class PrinterService
{
    /**
     * Create a new service instance.
     */
    public function __construct(
        private readonly PrinterContract $repository
    ) {}

    /**
     * Set printer as default.
     */
    public function setAsDefault(Printer $printer): Printer
    {
        // Unset other defaults for same terminal
        $this->repository->builder()
            ->where('terminal_id', $printer->terminal_id)
            ->where('id', '!=', $printer->id)
            ->update(['is_default' => false]);

        return $this->repository->update($printer, ['is_default' => true]);
    }

    /**
     * Get active printers for terminal.
     */
    public function getActivePrinters(int $terminalId): Collection
    {
        return $this->repository->builder()
            ->where('terminal_id', $terminalId)
            ->active()
            ->get();
    }

    /**
     * Get default printer for terminal.
     */
    public function getDefaultPrinter(int $terminalId): ?Printer
    {
        return $this->repository->builder()
            ->where('terminal_id', $terminalId)
            ->where('is_default', true)
            ->first();
    }
}
