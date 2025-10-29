<?php

declare(strict_types=1);

return [
    // Cart Panel
    'cart-panel' => [
        'current-order' => 'Current Order',
        'hold' => 'Hold',
        'recall' => 'Recall',
        'cart-empty' => 'Cart is empty',
        'add-products' => 'Add products to get started',
        'subtotal' => 'Subtotal',
        'order-discount' => 'Order Discount (:discount%)',
        'tax' => 'Tax',
        'total' => 'Total',
        'checkout' => 'Checkout',
        'discount' => 'Discount',
        'clear' => 'Clear',
    ],

    // Customer Card
    'customer-card' => [
        'customer' => 'Customer',
        'no-customer-selected' => 'No Customer Selected',
        'select' => 'Select Customer',
        'change' => 'Change',
        'points' => 'Points',
    ],

    // Product Grid
    'product-grid' => [
        'loading' => 'Loading products...',
        'no-products-found' => 'No products found',
        'out-of-stock' => 'Out of Stock',
    ],

    // Payment Dialog
    'payment-dialog' => [
        'title' => 'Process Payment',
        'description' => 'Enter payment details',
        'total-due' => 'Total Due',
        'amount-tendered' => 'Amount Tendered',
        'tendered' => 'Tendered',
        'change' => 'Change',
        'process-payment' => 'Process Payment',
        'processing' => 'Processing...',
        'exact-total' => 'Exact Total',
        'exact-amount' => 'Exact Amount',
        'clear' => 'Clear',
        'total-to-charge' => 'Total to Charge',
        'customer-will-pay' => 'Customer will pay via :method',
        'insufficient-amount' => 'Insufficient amount provided',
        'select-currency' => 'Select Currency',
        'customer-paying' => 'Customer Paying',
        'quick-add' => 'Quick Add',
        'card-payment' => 'Card Payment',
        'mobile-payment' => 'Mobile Payment',
        'amount-to-charge' => 'Amount to Charge',
        'card-instruction' => 'Please ask the customer to insert or tap their card on the payment terminal.',
        'mobile-instruction' => 'Please ask the customer to complete the payment using their mobile wallet app.',
        'amount-received' => 'Amount Received',
        'charging' => 'Charging',
        'summary' => 'Summary',
        'external-payment-note' => 'Payment will be processed externally. Click "Process Payment" to mark this order as paid.',
    ],

    // Receipt Dialog
    'receipt-dialog' => [
        'title' => 'Receipt',
        'description' => 'Transaction completed successfully',
        'loading' => 'Loading receipt...',
        'print' => 'Print',
        'email' => 'Email',
        'new-order' => 'New Order',
    ],

    // Payment Methods
    'payment-method' => [
        'cash' => 'Cash',
        'card' => 'Card',
        'mobile-money' => 'Mobile Money',
    ],

    // Discount Dialog
    'discount-dialog' => [
        'item-title' => 'Item Discount',
        'order-title' => 'Order Discount',
        'item-description' => 'Apply discount to :item',
        'order-description' => 'Apply discount to entire order',
        'apply-discount' => 'Apply Discount',
    ],

    // Customer Dialog
    'customer-dialog' => [
        'title' => 'Select Customer',
        'description' => 'Choose a customer for this order',
        'search-placeholder' => 'Search by name or email...',
        'no-customers-found' => 'No customers found',
        'clear-customer' => 'Clear Customer',
        'points' => 'Points',
    ],

    // Hold/Recall Dialog
    'hold-recall-dialog' => [
        'title' => 'Recall Order',
        'description' => 'Select an order to recall',
        'no-held-orders' => 'No held orders',
        'order-id' => 'Order #:id',
        'items' => ':count items',
    ],

    // Receipt Dialog
    'receipt-dialog' => [
        'title' => 'បង្កាន់ដៃ',
        'description' => 'ប្រតិបត្តិការបានបញ្ចប់ដោយជោគជ័យ',
        'loading' => 'កំពុងផ្ទុកបង្កាន់ដៃ...',
        'print' => 'បោះពុម្ព',
        'email' => 'អ៊ីមែល',
        'new-order' => 'ការបញ្ជាទិញថ្មី',
    ],

    // Validation Messages
    'validation' => [
        'customer-not-found' => 'រកមិនឃើញអតិថិជន',
        'shift-not-found' => 'រកមិនឃើញវេនការងារ',
        'order-discount-numeric' => 'ការបញ្ចុះតម្លៃត្រូវតែជាលេខ',
        'order-discount-min' => 'ការបញ្ចុះតម្លៃត្រូវតែយ៉ាងតិច 0',
        'items-required' => 'ត្រូវការយ៉ាងតិចមួយធាតុ',
        'items-min' => 'ត្រូវការយ៉ាងតិចមួយធាតុ',
        'product-id-required' => 'ត្រូវការលេខសម្គាល់ផលិតផល',
        'product-not-found' => 'រកមិនឃើញផលិតផល',
        'quantity-required' => 'ត្រូវការបរិមាណ',
        'quantity-min' => 'បរិមាណត្រូវតែយ៉ាងតិច 1',
        'payment-method-required' => 'ត្រូវការវិធីសាស្ត្របង់ប្រាក់',
        'payment-method-invalid' => 'វិធីសាស្ត្របង់ប្រាក់មិនត្រឹមត្រូវ',
        'amount-required' => 'ត្រូវការចំនួនទឹកប្រាក់',
        'amount-numeric' => 'ចំនួនទឹកប្រាក់ត្រូវតែជាលេខ',
        'amount-min' => 'ចំនួនទឹកប្រាក់ត្រូវតែយ៉ាងតិច 0',
        'cash-tendered-numeric' => 'សាច់ប្រាក់ដែលផ្តល់ត្រូវតែជាលេខ',
    ],
];
