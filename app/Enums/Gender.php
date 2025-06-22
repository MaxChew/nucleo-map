<?php

namespace App\Enums;

final class Gender extends Enum
{
    const MALE = 'MALE';
    const FEMALE = 'FEMALE';
    const NON_BINARY = 'NON_BINARY';
    const OTHER = 'OTHER';
    const PREFER_NOT_TO_SAY = 'PREFER_NOT_TO_SAY';

    const CLASSNAME = [
        self::MALE => 'text-blue-600',
        self::FEMALE => 'text-pink-600',
        self::NON_BINARY => 'text-purple-600',
        self::OTHER => 'text-gray-600',
        self::PREFER_NOT_TO_SAY => 'text-gray-400',
    ];

    const NAME = [
        self::MALE => 'Male',
        self::FEMALE => 'Female',
        self::NON_BINARY => 'Non-binary',
        self::OTHER => 'Other',
        self::PREFER_NOT_TO_SAY => 'Prefer not to say',
    ];

    const LIST = [
        self::MALE,
        self::FEMALE,
        self::NON_BINARY,
        self::OTHER,
        self::PREFER_NOT_TO_SAY,
    ];

    const MAP = [
        self::MALE => 'MALE',
        self::FEMALE => 'FEMALE',
        self::NON_BINARY => 'NON_BINARY',
        self::OTHER => 'OTHER',
        self::PREFER_NOT_TO_SAY => 'PREFER_NOT_TO_SAY',
    ];
}