<?php

declare(strict_types=1);

return [
    // Page titles
    'manage-movements' => '现金流动',
    'record-movement' => '记录现金流动',
    'movement-details' => '流动详情',
    'drawer-summary' => '收银箱摘要',

    // Form labels
    'shift' => '班次',
    'type' => '类型',
    'amount' => '金额',
    'payment-method' => '付款方式',
    'reason' => '原因',
    'notes' => '备注',
    'reference' => '参考',
    'performed-by' => '执行者',

    // Movement types
    'cash-in' => '现金收入',
    'cash-out' => '现金支出',
    'opening-balance' => '期初余额',
    'closing-balance' => '期末余额',
    'petty-cash' => '零用现金',
    'bank-deposit' => '银行存款',
    'adjustment' => '调整',
    'refund' => '退款',

    // Summary
    'opening-cash' => '期初现金',
    'total-cash-in' => '总现金收入',
    'total-cash-out' => '总现金支出',
    'total-sales' => '总销售额',
    'total-refunds' => '总退款',
    'current-balance' => '当前余额',
    'expected-balance' => '预期余额',

    // Messages
    'recorded-successfully' => '现金流动记录成功',
    'shift-must-be-open' => '班次必须开启才能记录现金流动',
    'invalid-type' => '无效的现金流动类型',

    // Validation
    'shift-required' => '请选择班次',
    'shift-invalid' => '选择的班次无效',
    'type-required' => '需要流动类型',
    'amount-required' => '需要金额',
    'amount-min' => '金额至少为 0.01',
    'reason-required' => '需要原因',
];
