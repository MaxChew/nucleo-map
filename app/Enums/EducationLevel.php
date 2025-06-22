<?php

namespace App\Enums;

final class EducationLevel extends Enum
{
    // 小学 (Primary School)
    public const P1 = 'P1'; // Primary 1 / Tahun 1
    public const P2 = 'P2'; // Primary 2 / Tahun 2
    public const P3 = 'P3'; // Primary 3 / Tahun 3
    public const P4 = 'P4'; // Primary 4 / Tahun 4
    public const P5 = 'P5'; // Primary 5 / Tahun 5
    public const P6 = 'P6'; // Primary 6 / Tahun 6

    // 初中 (Lower Secondary)
    public const F1 = 'F1'; // Form 1 / Tingkatan 1
    public const F2 = 'F2'; // Form 2 / Tingkatan 2
    public const F3 = 'F3'; // Form 3 / Tingkatan 3

    // 高中 (Upper Secondary)
    public const F4 = 'F4'; // Form 4 / Tingkatan 4
    public const F5 = 'F5'; // Form 5 / Tingkatan 5
    public const F6 = 'F6'; // Form 6 / Tingkatan 6
    /*
    // 大专 (Higher Education)
    const FOUNDATION = 'FOUNDATION';     // 大学预科
    const DIPLOMA = 'DIPLOMA';           // 专科文凭
    const DEGREE = 'DEGREE';            // 学士学位
    const MASTERS = 'MASTERS';          // 硕士学位
    const PHD = 'PHD';                  // 博士学位

    // 其他 (Others)
    const CERTIFICATE = 'CERTIFICATE';   // 证书课程
    const PROFESSIONAL = 'PROFESSIONAL'; // 专业资格
    const OTHERS = 'OTHERS';            // 其他 */

    // 显示名称
    public const NAME = [
        self::P1 => 'Primary 1',
        self::P2 => 'Primary 2',
        self::P3 => 'Primary 3',
        self::P4 => 'Primary 4',
        self::P5 => 'Primary 5',
        self::P6 => 'Primary 6',

        self::F1 => 'Form 1',
        self::F2 => 'Form 2',
        self::F3 => 'Form 3',
        self::F4 => 'Form 4',
        self::F5 => 'Form 5',
        self::F6 => 'Form 6',
        /*
        self::FOUNDATION => 'Foundation',
        self::DIPLOMA => 'Diploma',
        self::DEGREE => 'Degree',
        self::MASTERS => 'Masters',
        self::PHD => 'PhD',

        self::CERTIFICATE => 'Certificate',
        self::PROFESSIONAL => 'Professional Qualification',
        self::OTHERS => 'Others', */
    ];

    // 分组显示用
    public const GROUPS = [
        'Primary School' => [
            self::P1,
            self::P2,
            self::P3,
            self::P4,
            self::P5,
            self::P6,
        ],
        'Secondary School' => [
            self::F1,
            self::F2,
            self::F3,
            self::F4,
            self::F5,
            self::F6,
        ],
        /*
        'Higher Education' => [
            self::FOUNDATION,
            self::DIPLOMA,
            self::DEGREE,
            self::MASTERS,
            self::PHD,
        ],
        'Others' => [
            self::CERTIFICATE,
            self::PROFESSIONAL,
            self::OTHERS,
        ], */
    ];

    // 完整列表
    public const LIST = [
        self::P1,
        self::P2,
        self::P3,
        self::P4,
        self::P5,
        self::P6,
        self::F1,
        self::F2,
        self::F3,
        self::F4,
        self::F5,
        self::F6,
        /*
        self::FOUNDATION,
        self::DIPLOMA,
        self::DEGREE,
        self::MASTERS,
        self::PHD,
        self::CERTIFICATE,
        self::PROFESSIONAL,
        self::OTHERS, */
    ];

    // 映射关系
    public const MAP = [
        self::P1 => 'Standard 1',
        self::P2 => 'Standard 2',
        self::P3 => 'Standard 3',
        self::P4 => 'Standard 4',
        self::P5 => 'Standard 5',
        self::P6 => 'Standard 6',
        self::F1 => 'Form 1',
        self::F2 => 'Form 2',
        self::F3 => 'Form 3',
        self::F4 => 'Form 4',
        self::F5 => 'Form 5',
        self::F6 => 'Form 6',
        /*
        self::FOUNDATION => 'FOUNDATION',
        self::DIPLOMA => 'DIPLOMA',
        self::DEGREE => 'DEGREE',
        self::MASTERS => 'MASTERS',
        self::PHD => 'PHD',
        self::CERTIFICATE => 'CERTIFICATE',
        self::PROFESSIONAL => 'PROFESSIONAL',
        self::OTHERS => 'OTHERS', */
    ];
}
