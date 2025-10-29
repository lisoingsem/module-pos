<?php

declare(strict_types=1);

namespace Modules\POS\Models;

use App\Traits\HasUuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\POS\Database\Factories\ShiftFactory;
use Modules\POS\Enums\ShiftStatusEnum;

final class Shift extends Model
{
    use HasFactory;
    use HasUuidTrait;
    use SoftDeletes;

    /**
     * The table associated with the model.
     */
    protected $table = 'pos_shifts';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'uuid',
        'shiftable_type',
        'shiftable_id',
        'terminal_id',
        'status',
        'opening_cash',
        'closing_cash',
        'expected_cash',
        'actual_cash',
        'difference',
        'total_sales',
        'total_refunds',
        'total_transactions',
        'opened_at',
        'closed_at',
        'opened_by',
        'closed_by',
        'notes',
        'settings',
    ];

    /**
     * Get the owner of the shift (polymorphic).
     */
    public function shiftable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the terminal for the shift.
     */
    public function terminal(): BelongsTo
    {
        return $this->belongsTo(Terminal::class);
    }

    /**
     * Get the user who opened the shift.
     */
    public function openedBy(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'opened_by');
    }

    /**
     * Get the user who closed the shift.
     */
    public function closedBy(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'closed_by');
    }

    /**
     * Get the cash movements for the shift.
     */
    public function cashMovements(): HasMany
    {
        return $this->hasMany(CashMovement::class);
    }

    /**
     * Check if shift is open.
     */
    public function isOpen(): bool
    {
        return ShiftStatusEnum::OPEN === $this->status || $this->status->value === ShiftStatusEnum::OPEN->value;
    }

    /**
     * Check if shift is closed.
     */
    public function isClosed(): bool
    {
        return ShiftStatusEnum::CLOSED === $this->status || $this->status->value === ShiftStatusEnum::CLOSED->value;
    }

    /**
     * Check if shift is suspended.
     */
    public function isSuspended(): bool
    {
        return ShiftStatusEnum::SUSPENDED === $this->status || $this->status->value === ShiftStatusEnum::SUSPENDED->value;
    }

    /**
     * Check if shift can be closed.
     */
    public function canBeClosed(): bool
    {
        return $this->isOpen() || $this->isSuspended();
    }

    /**
     * Calculate expected cash.
     */
    public function calculateExpectedCash(): float
    {
        return $this->opening_cash + $this->total_sales - $this->total_refunds;
    }

    /**
     * Get current cash balance.
     */
    public function getCurrentBalance(): float
    {
        return $this->opening_cash + $this->total_sales - $this->total_refunds;
    }

    /**
     * Scope to get open shifts.
     */
    public function scopeOpen($query)
    {
        return $query->where('status', ShiftStatusEnum::OPEN);
    }

    /**
     * Scope to get closed shifts.
     */
    public function scopeClosed($query)
    {
        return $query->where('status', ShiftStatusEnum::CLOSED);
    }

    /**
     * Scope to filter by terminal.
     */
    public function scopeByTerminal($query, int $terminalId)
    {
        return $query->where('terminal_id', $terminalId);
    }

    /**
     * Scope to filter by date range.
     */
    public function scopeByDateRange($query, string $startDate, string $endDate)
    {
        return $query->whereBetween('opened_at', [$startDate, $endDate]);
    }

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): ShiftFactory
    {
        return ShiftFactory::new();
    }

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'status' => ShiftStatusEnum::class,
            'opening_cash' => 'decimal:2',
            'closing_cash' => 'decimal:2',
            'expected_cash' => 'decimal:2',
            'actual_cash' => 'decimal:2',
            'difference' => 'decimal:2',
            'total_sales' => 'decimal:2',
            'total_refunds' => 'decimal:2',
            'settings' => 'array',
            'opened_at' => 'datetime',
            'closed_at' => 'datetime',
        ];
    }
}
