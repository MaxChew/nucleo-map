<?php

namespace App\Enums;

final class PaymentType extends Enum
{
    const BANK = 'BANK';
    const CHEQUE = 'CHEQUE';
    const TOUCH_N_GO = 'TOUCH_N_GO';
    const BOOST = 'BOOST';

    // 用于前端显示的 Tailwind CSS 类名
    const CLASSNAME = [
        self::BANK => 'text-blue-600',
        self::CHEQUE => 'text-gray-600',
        self::TOUCH_N_GO => 'text-green-600',
        self::BOOST => 'text-purple-600',
    ];

    // 显示名称
    const NAME = [
        self::BANK => 'Bank Account',
        self::CHEQUE => 'Cheque',
        self::TOUCH_N_GO => 'Touch n Go',
        self::BOOST => 'Boost',
    ];

    // 所有支付方式列表
    const LIST = [
        self::BANK,
        self::CHEQUE,
        self::TOUCH_N_GO,
        self::BOOST,
    ];

    // 活跃的支付方式列表（如果将来有些支付方式需要停用）
    const LIST_ACTIVE = [
        self::BANK,
        self::CHEQUE,
        self::TOUCH_N_GO,
        self::BOOST,
    ];

    // 用于数据库存储的映射值
    const MAP = [
        self::BANK => 'Bank',
        self::CHEQUE => 'Cheque',
        self::TOUCH_N_GO => 'Touch N Go',
        self::BOOST => 'Boost',
    ];
}