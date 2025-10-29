<?php

declare(strict_types=1);

namespace Modules\POS\Enums;

enum PrinterTypeEnum: string
{
    case RECEIPT = 'receipt';
    case KITCHEN = 'kitchen';
    case LABEL = 'label';
    case REPORT = 'report';

    /**
     * Get all values.
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Get label for the type.
     */
    public function label(): string
    {
        return match ($this) {
            self::RECEIPT => 'Receipt Printer',
            self::KITCHEN => 'Kitchen Printer',
            self::LABEL => 'Label Printer',
            self::REPORT => 'Report Printer',
        };
    }
}
