<?php

namespace App\Enums;

final class TitlePrefix extends Enum
{
    const Mr = 'Mr';
    const Ms = 'Ms';
    const Mrs = 'Mrs';
    const Datuk = 'Datuk';
    const Datin = 'Datin';
    const Tuan = 'Tuan';

    const CLASSNAME = [
    ];

    const NAME = [
        self::Mr => 'Mr',
        self::Ms => 'Ms',
        self::Mrs => 'Mrs',
        self::Datuk => 'Datuk',
        self::Datin => 'Datin',
        self::Tuan => 'Tuan',
    ];

    const LIST = [
        self::Mr,
        self::Ms,
        self::Mrs,
        self::Datuk,
        self::Datin,
        self::Tuan,
    ];

    const MAP = [
        self::Mr => 'Mr.',
        self::Ms => 'Ms.',
        self::Mrs => 'Mrs.',
        self::Datuk => 'Datuk.',
        self::Datin => 'Datin.',
        self::Tuan => 'Tuan.',
    ];
}