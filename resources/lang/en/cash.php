<?php

declare(strict_types=1);

return [
    // Page titles
    'manage-movements' => 'Cash Movements',
    'record-movement' => 'Record Cash Movement',
    'movement-details' => 'Movement Details',
    'drawer-summary' => 'Cash Drawer Summary',

    // Form labels
    'shift' => 'Shift',
    'type' => 'Type',
    'amount' => 'Amount',
    'payment-method' => 'Payment Method',
    'reason' => 'Reason',
    'notes' => 'Notes',
    'reference' => 'Reference',
    'performed-by' => 'Performed By',

    // Movement types
    'cash-in' => 'Cash In',
    'cash-out' => 'Cash Out',
    'opening-balance' => 'Opening Balance',
    'closing-balance' => 'Closing Balance',
    'petty-cash' => 'Petty Cash',
    'bank-deposit' => 'Bank Deposit',
    'adjustment' => 'Adjustment',
    'refund' => 'Refund',

    // Summary
    'opening-cash' => 'Opening Cash',
    'total-cash-in' => 'Total Cash In',
    'total-cash-out' => 'Total Cash Out',
    'total-sales' => 'Total Sales',
    'total-refunds' => 'Total Refunds',
    'current-balance' => 'Current Balance',
    'expected-balance' => 'Expected Balance',

    // Messages
    'recorded-successfully' => 'Cash movement recorded successfully',
    'shift-must-be-open' => 'Shift must be open to record cash movement',
    'invalid-type' => 'Invalid cash movement type',

    // Validation
    'shift-required' => 'Please select a shift',
    'shift-invalid' => 'Invalid shift selected',
    'type-required' => 'Movement type is required',
    'amount-required' => 'Amount is required',
    'amount-min' => 'Amount must be at least 0.01',
    'reason-required' => 'Reason is required',
];
