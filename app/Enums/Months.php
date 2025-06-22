<?php

namespace App\Enums;


final class Months extends Enum
{
    const JAN = '1';
    const FEB = '2';
    const MAR = '3';
    const APR = '4';
    const MAY = '5';
    const JUN = '6';
    const JUL = '7';
    const AUG = '8';
    const SEP = '9';
    const OCT = '10';
    const NOV = '11';
    const DEC = '12';

    const CLASSNAME = [
    ];

    const NAME = [
        'January',
        'February',
        'March',
        'April',
        'May',
        'June',
        'July',
        'August',
        'September',
        'October',
        'November',
        'December',
    ];

    const LIST_ACTIVE = [
        self::JAN,
        self::FEB,
        self::MAR,
        self::APR,
        self::MAY,
        self::JUN,
        self::JUL,
        self::AUG,
        self::SEP,
        self::OCT, 
        self::NOV, 
        self::DEC, 
    ];
 
    const LIST_INACTIVE = [
    ];

    const LIST = [
        self::JAN,
        self::FEB,
        self::MAR,
        self::APR,
        self::MAY,
        self::JUN,
        self::JUL,
        self::AUG,
        self::SEP,
        self::OCT, 
        self::NOV, 
        self::DEC, 
    ];

    const MAP = [
        self::JAN => 'January',
        self::FEB => 'February',
        self::MAR => 'March',
        self::APR => 'April',
        self::MAY => 'May',
        self::JUN => 'June',
        self::JUL => 'July',
        self::AUG => 'August',
        self::SEP => 'September',
        self::OCT => 'October',
        self::NOV => 'November',
        self::DEC => 'December',
    ];
}
