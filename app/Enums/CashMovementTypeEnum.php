<?php

declare(strict_types=1);

namespace Modules\POS\Enums;

enum CashMovementTypeEnum: string
{
    case CASH_IN = 'cash_in';
    case CASH_OUT = 'cash_out';
    case OPENING_BALANCE = 'opening_balance';
    case CLOSING_BALANCE = 'closing_balance';
    case PETTY_CASH = 'petty_cash';
    case BANK_DEPOSIT = 'bank_deposit';
    case ADJUSTMENT = 'adjustment';
    case REFUND = 'refund';

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
            self::CASH_IN => 'Cash In',
            self::CASH_OUT => 'Cash Out',
            self::OPENING_BALANCE => 'Opening Balance',
            self::CLOSING_BALANCE => 'Closing Balance',
            self::PETTY_CASH => 'Petty Cash',
            self::BANK_DEPOSIT => 'Bank Deposit',
            self::ADJUSTMENT => 'Adjustment',
            self::REFUND => 'Refund',
        };
    }
}
