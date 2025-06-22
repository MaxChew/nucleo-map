<?php

namespace App\Enums;

class ServiceType extends Enum
{
    const SPECT = 'SPECT';
    const PET = 'PET';
    const RAI = 'RAI';
    const PRRT = 'PRRT';
    const PSMA = 'PSMA';
    const SIRT = 'SIRT';
    const MIBG = 'MIBG';
    const BONE_P = 'BONE-P';
    const RSO = 'RSO';
    const AC = 'AC';

    /**
     * 获取服务类型标签映射
     */
    public static function getLabels(): array
    {
        return [
            self::SPECT => 'SPECT 扫描',
            self::PET => 'PET 扫描',
            self::RAI => '碘-131 治疗',
            self::PRRT => 'PRRT 治疗',
            self::PSMA => 'PSMA 治疗',
            self::SIRT => 'SIRT 治疗',
            self::MIBG => 'MIBG 治疗',
            self::BONE_P => '骨痛治疗',
            self::RSO => 'RSO 治疗',
            self::AC => 'AC 治疗',
        ];
    }

    /**
     * 获取服务类型描述
     */
    public static function getDescriptions(): array
    {
        return [
            self::SPECT => '单光子发射计算机断层扫描',
            self::PET => '正电子发射断层扫描',
            self::RAI => '放射性碘治疗',
            self::PRRT => '肽受体放射性核素治疗',
            self::PSMA => '前列腺特异性膜抗原治疗',
            self::SIRT => '选择性内放射治疗',
            self::MIBG => '间碘苄胍治疗',
            self::BONE_P => '骨痛缓解治疗',
            self::RSO => '放射性滑膜切除术',
            self::AC => '关节软骨治疗',
        ];
    }

    /**
     * 获取服务类型颜色
     */
    public static function getColors(): array
    {
        return [
            self::SPECT => 'blue',
            self::PET => 'green',
            self::RAI => 'yellow',
            self::PRRT => 'purple',
            self::PSMA => 'pink',
            self::SIRT => 'indigo',
            self::MIBG => 'red',
            self::BONE_P => 'orange',
            self::RSO => 'teal',
            self::AC => 'gray',
        ];
    }
} 