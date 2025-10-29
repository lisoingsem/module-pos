<?php

declare(strict_types=1);

return [
    // Page titles
    'manage-shifts' => '管理班次',
    'open-shift' => '开启班次',
    'shift-details' => '班次详情',

    // Form labels
    'terminal' => '终端',
    'opening-cash' => '期初现金',
    'closing-cash' => '期末现金',
    'expected-cash' => '预期现金',
    'actual-cash' => '实际现金',
    'difference' => '差异',
    'total-sales' => '总销售额',
    'total-refunds' => '总退款',
    'total-transactions' => '总交易数',
    'opened-at' => '开启时间',
    'closed-at' => '关闭时间',
    'opened-by' => '开启者',
    'closed-by' => '关闭者',
    'notes' => '备注',
    'duration' => '持续时间',

    // Statuses
    'open' => '开启',
    'closed' => '关闭',
    'suspended' => '暂停',

    // Actions
    'close-shift' => '关闭班次',
    'suspend-shift' => '暂停班次',
    'resume-shift' => '恢复班次',

    // Messages
    'opened-successfully' => '班次开启成功',
    'closed-successfully' => '班次关闭成功',
    'suspended-successfully' => '班次暂停成功',
    'resumed-successfully' => '班次恢复成功',
    'shift-already-open' => '该终端已有开启的班次',
    'shift-cannot-be-closed' => '无法关闭此班次',
    'shift-must-be-open' => '班次必须开启才能执行此操作',
    'shift-must-be-suspended' => '班次必须暂停才能恢复',

    // Validation
    'terminal-required' => '请选择终端',
    'terminal-invalid' => '选择的终端无效',
    'opening-cash-required' => '需要期初现金金额',
    'opening-cash-min' => '期初现金至少为 0',
    'actual-cash-required' => '需要实际现金金额',
    'actual-cash-min' => '实际现金至少为 0',
];
