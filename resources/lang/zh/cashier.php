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
        'title' => '收据',
        'description' => '交易成功完成',
        'loading' => '正在加载收据...',
        'print' => '打印',
        'email' => '电子邮件',
        'new-order' => '新订单',
    ],

    // Validation Messages
    'validation' => [
        'customer-not-found' => '找不到客户',
        'shift-not-found' => '找不到班次',
        'order-discount-numeric' => '订单折扣必须是数字',
        'order-discount-min' => '订单折扣必须至少为0',
        'items-required' => '至少需要一个项目',
        'items-min' => '至少需要一个项目',
        'product-id-required' => '需要产品ID',
        'product-not-found' => '找不到产品',
        'quantity-required' => '需要数量',
        'quantity-min' => '数量必须至少为1',
        'payment-method-required' => '需要付款方式',
        'payment-method-invalid' => '无效的付款方式',
        'amount-required' => '需要金额',
        'amount-numeric' => '金额必须是数字',
        'amount-min' => '金额必须至少为0',
        'cash-tendered-numeric' => '提供的现金必须是数字',
    ],
];
