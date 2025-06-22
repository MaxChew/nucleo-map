<?php

namespace App\Enums;

final class WalletTransactionType extends Enum
{
    // 交易类型
    const PURCHASE = 'PURCHASE';      // 购买课时
    const USAGE = 'USAGE';           // 使用课时
    const REFUND = 'REFUND';         // 退还课时
    const TRANSFER = 'TRANSFER';      // 转移课时
    const EXPIRED = 'EXPIRED';       // 过期扣除
    const ADJUSTMENT = 'ADJUSTMENT';  // 管理员调整

    const CLASSNAME = [
        self::PURCHASE => 'text-green-600',
        self::USAGE => 'text-blue-600',
        self::REFUND => 'text-yellow-600',
        self::TRANSFER => 'text-purple-600',
        self::EXPIRED => 'text-red-600',
        self::ADJUSTMENT => 'text-gray-600',
    ];

    const NAME = [
        self::PURCHASE => 'Purchase',
        self::USAGE => 'Usage',
        self::REFUND => 'Refund',
        self::TRANSFER => 'Transfer',
        self::EXPIRED => 'Expired',
        self::ADJUSTMENT => 'Adjustment',
    ];

    const LIST = [
        self::PURCHASE,
        self::USAGE,
        self::REFUND,
        self::TRANSFER,
        self::EXPIRED,
        self::ADJUSTMENT,
    ];

    const MAP = [
        self::PURCHASE => 'PURCHASE',
        self::USAGE => 'USAGE',
        self::REFUND => 'REFUND',
        self::TRANSFER => 'TRANSFER',
        self::EXPIRED => 'EXPIRED',
        self::ADJUSTMENT => 'ADJUSTMENT',
    ];

    const TYPE_IN_LIST = [
        self::PURCHASE,
        self::TRANSFER,
    ]; 

    const TYPE_OUT_LIST = [
        self::USAGE,
        self::TRANSFER,
    ]; 
}