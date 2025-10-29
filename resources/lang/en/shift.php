<?php

declare(strict_types=1);

return [
    // Page titles
    'manage-shifts' => 'Manage Shifts',
    'open-shift' => 'Open Shift',
    'shift-details' => 'Shift Details',

    // Form labels
    'terminal' => 'Terminal',
    'opening-cash' => 'Opening Cash',
    'closing-cash' => 'Closing Cash',
    'expected-cash' => 'Expected Cash',
    'actual-cash' => 'Actual Cash',
    'difference' => 'Difference',
    'total-sales' => 'Total Sales',
    'total-refunds' => 'Total Refunds',
    'total-transactions' => 'Total Transactions',
    'opened-at' => 'Opened At',
    'closed-at' => 'Closed At',
    'opened-by' => 'Opened By',
    'closed-by' => 'Closed By',
    'notes' => 'Notes',
    'duration' => 'Duration',

    // Statuses
    'open' => 'Open',
    'closed' => 'Closed',
    'suspended' => 'Suspended',

    // Actions
    'close-shift' => 'Close Shift',
    'suspend-shift' => 'Suspend Shift',
    'resume-shift' => 'Resume Shift',

    // Messages
    'opened-successfully' => 'Shift opened successfully',
    'closed-successfully' => 'Shift closed successfully',
    'suspended-successfully' => 'Shift suspended successfully',
    'resumed-successfully' => 'Shift resumed successfully',
    'shift-already-open' => 'A shift is already open for this terminal',
    'shift-cannot-be-closed' => 'This shift cannot be closed',
    'shift-must-be-open' => 'Shift must be open to perform this action',
    'shift-must-be-suspended' => 'Shift must be suspended to resume',

    // Validation
    'terminal-required' => 'Please select a terminal',
    'terminal-invalid' => 'Invalid terminal selected',
    'opening-cash-required' => 'Opening cash amount is required',
    'opening-cash-min' => 'Opening cash must be at least 0',
    'actual-cash-required' => 'Actual cash amount is required',
    'actual-cash-min' => 'Actual cash must be at least 0',
];
