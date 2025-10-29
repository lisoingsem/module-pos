<?php

declare(strict_types=1);

namespace Modules\POS\Services;

use Illuminate\Database\Eloquent\Collection;
use Modules\POS\Contracts\TerminalContract;
use Modules\POS\Models\Terminal;

final class TerminalService
{
    /**
     * Create a new service instance.
     */
    public function __construct(
        private readonly TerminalContract $repository
    ) {}

    /**
     * Create a new terminal.
     */
    public function create(array $data): Terminal
    {
        return $this->repository->create($data);
    }

    /**
     * Update an existing terminal.
     */
    public function update(Terminal $terminal, array $data): Terminal
    {
        return $this->repository->update($terminal, $data);
    }

    /**
     * Delete a terminal.
     */
    public function delete(Terminal $terminal): bool
    {
        return $this->repository->delete($terminal);
    }

    /**
     * Get all active terminals.
     */
    public function getActiveTerminals(): Collection
    {
        return $this->repository->builder()
            ->active()
            ->orderBy('name')
            ->get();
    }
}
