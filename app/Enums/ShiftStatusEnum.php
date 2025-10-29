<?php

declare(strict_types=1);

namespace Modules\POS\Enums;

enum ShiftStatusEnum: string
{
    case OPEN = 'open';
    case CLOSED = 'closed';
    case SUSPENDED = 'suspended';

    /**
     * Get all values.
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Get label for the status.
     */
    public function label(): string
    {
        return match ($this) {
            self::OPEN => 'Open',
            self::CLOSED => 'Closed',
            self::SUSPENDED => 'Suspended',
        };
    }
}
