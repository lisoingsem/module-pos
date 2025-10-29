<?php

declare(strict_types=1);

namespace Modules\POS\Models;

use App\Traits\HasUuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\POS\Database\Factories\CashMovementFactory;
use Modules\POS\Enums\CashMovementTypeEnum;

final class CashMovement extends Model
{
    use HasFactory;
    use HasUuidTrait;
    use SoftDeletes;

    /**
     * The table associated with the model.
     */
    protected $table = 'pos_cash_movements';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'uuid',
        'shift_id',
        'type',
        'amount',
        'payment_method',
        'reason',
        'notes',
        'reference',
        'performed_by',
    ];

    /**
     * Get the shift for the cash movement.
     */
    public function shift(): BelongsTo
    {
        return $this->belongsTo(Shift::class);
    }

    /**
     * Get the user who performed the movement.
     */
    public function performedBy(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'performed_by');
    }

    /**
     * Check if movement is cash in.
     */
    public function isCashIn(): bool
    {
        return CashMovementTypeEnum::CASH_IN === $this->type
            || $this->type->value === CashMovementTypeEnum::CASH_IN->value;
    }

    /**
     * Check if movement is cash out.
     */
    public function isCashOut(): bool
    {
        return CashMovementTypeEnum::CASH_OUT === $this->type
            || $this->type->value === CashMovementTypeEnum::CASH_OUT->value;
    }

    /**
     * Scope to filter by shift.
     */
    public function scopeByShift($query, int $shiftId)
    {
        return $query->where('shift_id', $shiftId);
    }

    /**
     * Scope to filter by type.
     */
    public function scopeByType($query, CashMovementTypeEnum|string $type)
    {
        $typeValue = $type instanceof CashMovementTypeEnum ? $type->value : $type;

        return $query->where('type', $typeValue);
    }

    /**
     * Scope to get cash in movements.
     */
    public function scopeCashIn($query)
    {
        return $query->where('type', CashMovementTypeEnum::CASH_IN);
    }

    /**
     * Scope to get cash out movements.
     */
    public function scopeCashOut($query)
    {
        return $query->where('type', CashMovementTypeEnum::CASH_OUT);
    }

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): CashMovementFactory
    {
        return CashMovementFactory::new();
    }

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'type' => CashMovementTypeEnum::class,
            'amount' => 'decimal:2',
        ];
    }
}
