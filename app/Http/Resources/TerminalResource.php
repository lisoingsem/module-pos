<?php

declare(strict_types=1);

namespace Modules\POS\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class TerminalResource extends JsonResource
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
            'name' => $this->name,
            'code' => $this->code,
            'location' => $this->location,
            'status' => $this->status,
            'status_label' => $this->status?->label(),
            'settings' => $this->settings,
            'last_used_at' => $this->last_used_at?->toISOString(),
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
            'deleted_at' => $this->deleted_at?->toISOString(),

            // Conditional relationships
            'terminalable' => $this->when($this->relationLoaded('terminalable'), $this->terminalable),
            'shifts_count' => $this->when(null !== $this->shifts_count, $this->shifts_count),
            'printers_count' => $this->when(null !== $this->printers_count, $this->printers_count),
        ];
    }
}
