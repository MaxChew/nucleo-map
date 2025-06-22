<?php

namespace App\Enums;

final class MeetMethod extends Enum
{
    // 定义常量
    const ONLINE = 'online';
    const OFFLINE = 'offline';

    // 显示名称
    const NAME = [
        self::ONLINE => '线上',
        self::OFFLINE => '线下'
    ];

    // 完整列表
    const LIST = [
        self::ONLINE,
        self::OFFLINE
    ];

    // 分组显示用
    const GROUPS = [
        'All Methods' => [
            self::ONLINE,
            self::OFFLINE
        ]
    ];

    // 映射关系
    const MAP = [
        self::ONLINE => 'Online Meeting',
        self::OFFLINE => 'Offline Meeting'
    ];

    /**
     * 检查是否为线上会议
     */
    public static function isOnline($value): bool
    {
        return $value === self::ONLINE;
    }

    /**
     * 检查是否为线下会议
     */
    public static function isOffline($value): bool
    {
        return $value === self::OFFLINE;
    }

    /**
     * 获取所有可用的会面方式
     */
    public static function getAvailableMethods(): array
    {
        return self::NAME;
    }

    /**
     * 获取显示名称
     */
    public static function getLabel($value): string
    {
        return self::NAME[$value] ?? $value;
    }
}