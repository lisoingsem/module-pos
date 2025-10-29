<?php

declare(strict_types=1);

namespace Modules\POS\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class ShiftResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'terminal_id' => $this->terminal_id,
            'status' => $this->status,
            'status_label' => $this->status?->label(),

            // Cash tracking
            'opening_cash' => $this->opening_cash,
            'closing_cash' => $this->closing_cash,
            'expected_cash' => $this->expected_cash,
            'actual_cash' => $this->actual_cash,
            'difference' => $this->difference,
            'current_balance' => $this->getCurrentBalance(),

            // Sales summaries
            'total_sales' => $this->total_sales,
            'total_refunds' => $this->total_refunds,
            'total_transactions' => $this->total_transactions,

            // Timestamps
            'opened_at' => $this->opened_at?->toISOString(),
            'closed_at' => $this->closed_at?->toISOString(),
            'duration' => $this->opened_at && $this->closed_at
                ? $this->opened_at->diffInMinutes($this->closed_at)
                : ($this->opened_at ? $this->opened_at->diffInMinutes(now()) : null),

            // Notes
            'notes' => $this->notes,
            'settings' => $this->settings,

            // Relationships
            'terminal' => $this->when($this->relationLoaded('terminal'), fn () => new TerminalResource($this->terminal)),
            'opened_by' => $this->when($this->relationLoaded('openedBy'), fn () => [
                'id' => $this->openedBy?->id,
                'name' => $this->openedBy?->name,
            ]),
            'closed_by' => $this->when($this->relationLoaded('closedBy'), fn () => [
                'id' => $this->closedBy?->id,
                'name' => $this->closedBy?->name,
            ]),
            'cash_movements_count' => $this->when(null !== $this->cash_movements_count, $this->cash_movements_count),

            // Timestamps
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
            'deleted_at' => $this->deleted_at?->toISOString(),
        ];
    }
}
