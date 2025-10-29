<?php

declare(strict_types=1);

namespace Modules\POS\Models;

use App\Traits\HasUuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\POS\Database\Factories\PrinterFactory;
use Modules\POS\Enums\PrinterTypeEnum;

final class Printer extends Model
{
    use HasFactory;
    use HasUuidTrait;
    use SoftDeletes;

    /**
     * The table associated with the model.
     */
    protected $table = 'pos_printers';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'uuid',
        'terminal_id',
        'name',
        'type',
        'connection_type',
        'ip_address',
        'port',
        'device_path',
        'paper_width',
        'is_default',
        'is_active',
        'settings',
    ];

    /**
     * Get the terminal for the printer.
     */
    public function terminal(): BelongsTo
    {
        return $this->belongsTo(Terminal::class);
    }

    /**
     * Check if printer is active.
     */
    public function isActive(): bool
    {
        return $this->is_active;
    }

    /**
     * Check if printer is default.
     */
    public function isDefault(): bool
    {
        return $this->is_default;
    }

    /**
     * Scope to get active printers.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get printers by type.
     */
    public function scopeByType($query, PrinterTypeEnum|string $type)
    {
        $typeValue = $type instanceof PrinterTypeEnum ? $type->value : $type;

        return $query->where('type', $typeValue);
    }

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): PrinterFactory
    {
        return PrinterFactory::new();
    }

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'type' => PrinterTypeEnum::class,
            'port' => 'integer',
            'paper_width' => 'integer',
            'is_default' => 'boolean',
            'is_active' => 'boolean',
            'settings' => 'array',
        ];
    }
}
