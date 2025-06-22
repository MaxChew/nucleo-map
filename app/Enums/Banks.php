<?php

namespace App\Enums;

final class Banks  extends Enum
{
    // Bank Constants
    const MAYBANK = 'MAYBANK';
    const CIMB = 'CIMB';
    const PUBLIC_BANK = 'PUBLIC_BANK';
    const RHB = 'RHB';
    const HONG_LEONG = 'HONG_LEONG';
    const AMBANK = 'AMBANK';
    const BSN = 'BSN';
    const AFFIN = 'AFFIN';
    const BANK_ISLAM = 'BANK_ISLAM';
    const BANK_RAKYAT = 'BANK_RAKYAT';
    const OCBC = 'OCBC';
    const UOB = 'UOB';
    const HSBC = 'HSBC';
    const STANDARD_CHARTERED = 'STANDARD_CHARTERED';
    const CITI_BANK = 'CITI_BANK';
    const ALLIANCE_BANK = 'ALLIANCE_BANK';

    // Bank Display Names
    const MAP = [
        self::MAYBANK => 'Maybank',
        self::CIMB => 'CIMB Bank',
        self::PUBLIC_BANK => 'Public Bank',
        self::RHB => 'RHB Bank',
        self::HONG_LEONG => 'Hong Leong Bank',
        self::AMBANK => 'AmBank',
        self::BSN => 'Bank Simpanan Nasional (BSN)',
        self::AFFIN => 'Affin Bank',
        self::BANK_ISLAM => 'Bank Islam',
        self::BANK_RAKYAT => 'Bank Rakyat',
        self::OCBC => 'OCBC Bank',
        self::UOB => 'United Overseas Bank (UOB)',
        self::HSBC => 'HSBC Bank',
        self::STANDARD_CHARTERED => 'Standard Chartered Bank',
        self::CITI_BANK => 'Citi Bank',
        self::ALLIANCE_BANK => 'Alliance Bank',
    ];
}
