<?php

declare(strict_types=1);

namespace Modules\POS\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class PrinterResource extends JsonResource
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
            'name' => $this->name,
            'type' => $this->type,
            'type_label' => $this->type?->label(),
            'connection_type' => $this->connection_type,
            'ip_address' => $this->ip_address,
            'port' => $this->port,
            'device_path' => $this->device_path,
            'paper_width' => $this->paper_width,
            'is_default' => $this->is_default,
            'is_active' => $this->is_active,
            'settings' => $this->settings,

            // Relationships
            'terminal' => $this->when($this->relationLoaded('terminal'), fn () => new TerminalResource($this->terminal)),

            // Timestamps
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
        ];
    }
}
