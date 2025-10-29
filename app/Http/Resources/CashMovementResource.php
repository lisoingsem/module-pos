<?php

declare(strict_types=1);

namespace Modules\POS\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class CashMovementResource extends JsonResource
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
            'shift_id' => $this->shift_id,
            'type' => $this->type,
            'type_label' => $this->type?->label(),
            'amount' => $this->amount,
            'payment_method' => $this->payment_method,
            'reason' => $this->reason,
            'notes' => $this->notes,
            'reference' => $this->reference,

            // Relationships
            'shift' => $this->when($this->relationLoaded('shift'), fn () => new ShiftResource($this->shift)),
            'performed_by' => $this->when($this->relationLoaded('performedBy'), fn () => [
                'id' => $this->performedBy?->id,
                'name' => $this->performedBy?->name,
            ]),

            // Timestamps
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
            'deleted_at' => $this->deleted_at?->toISOString(),
        ];
    }
}
