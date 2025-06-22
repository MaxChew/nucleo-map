<?php

namespace App\Enums;

final class RegistrationStatus
{
    const UNPAID = 'UNPAID';
    const PAID = 'PAID';

    const CLASSNAME = [
        self::UNPAID => 'red-500',  // Unpaid status with red color
        self::PAID => 'green-500',  // Paid status with green color
    ];

    const NAME = [
        self::UNPAID,
        self::PAID,
    ];

    const LIST = [
        self::UNPAID,
        self::PAID,
    ];

    const MAP = [
        self::UNPAID => 'Unpaid',
        self::PAID => 'Paid',
    ];

    /**
     * Get the map of registration status constants to their display names.
     *
     * @return array
     */
    public static function getMap(): array
    {
        return self::MAP;
    }
}
