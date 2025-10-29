<?php

declare(strict_types=1);

namespace Modules\POS\Models;

use App\Traits\HasUuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\POS\Database\Factories\TerminalFactory;
use Modules\POS\Enums\TerminalStatusEnum;

final class Terminal extends Model
{
    use HasFactory;
    use HasUuidTrait;
    use SoftDeletes;

    /**
     * The table associated with the model.
     */
    protected $table = 'pos_terminals';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'uuid',
        'terminalable_type',
        'terminalable_id',
        'name',
        'code',
        'location',
        'status',
        'settings',
        'last_used_at',
    ];

    /**
     * Get the owner of the terminal (polymorphic).
     */
    public function terminalable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the shifts for the terminal.
     */
    public function shifts(): HasMany
    {
        return $this->hasMany(Shift::class);
    }

    /**
     * Get the printers for the terminal.
     */
    public function printers(): HasMany
    {
        return $this->hasMany(Printer::class);
    }

    /**
     * Check if terminal is active.
     */
    public function isActive(): bool
    {
        return TerminalStatusEnum::ACTIVE === $this->status || $this->status->value === TerminalStatusEnum::ACTIVE->value;
    }

    /**
     * Check if terminal is inactive.
     */
    public function isInactive(): bool
    {
        return TerminalStatusEnum::INACTIVE === $this->status || $this->status->value === TerminalStatusEnum::INACTIVE->value;
    }

    /**
     * Check if terminal is under maintenance.
     */
    public function isUnderMaintenance(): bool
    {
        return TerminalStatusEnum::MAINTENANCE === $this->status || $this->status->value === TerminalStatusEnum::MAINTENANCE->value;
    }

    /**
     * Scope to get active terminals.
     */
    public function scopeActive($query)
    {
        return $query->where('status', TerminalStatusEnum::ACTIVE);
    }

    /**
     * Scope to filter by status.
     */
    public function scopeByStatus($query, TerminalStatusEnum|string $status)
    {
        $statusValue = $status instanceof TerminalStatusEnum ? $status->value : $status;

        return $query->where('status', $statusValue);
    }

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): TerminalFactory
    {
        return TerminalFactory::new();
    }

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'status' => TerminalStatusEnum::class,
            'settings' => 'array',
            'last_used_at' => 'datetime',
        ];
    }
}
