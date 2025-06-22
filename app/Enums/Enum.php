<?php

namespace App\Enums;

use BenSampo\Enum\Enum as BaseEnum;
use Illuminate\Support\Arr;

abstract class Enum extends BaseEnum
{
    public static function getCustomArray()
    {
        // 使用反射获取常量名称和值
        $constants = (new \ReflectionClass(static::class))->getConstants();
        return array_flip($constants);
    }

    public static function getCustomValue($map_name, $key)
    {
        if (isset(static::$$map_name)) {
            return Arr::get(static::$$map_name, $key);
        }
        return null;
    }

    public static function getMap()
    {
        return defined('static::MAP') ? static::MAP : null;
    }

    public static function getMapValueByKey($key)
    {
        return Arr::get(static::getMap(), $key);
    }

    public static function getClassByKey($key)
    {
        return defined('static::CLASSNAME') ? Arr::get(static::CLASSNAME, $key) : null;
    }

    public static function getCustomValueByKey($const_name, $key)
    {
        $constName = strtoupper($const_name);
        
        // 如果常量存在且有对应的映射值,返回映射值
        if (defined("static::$constName")) {
            $value = Arr::get(constant("static::$constName"), strtoupper($key));
            if (!is_null($value)) {
                return $value;
            }
        }
        
        // 如果没有找到映射,返回原始的 key
        return $key;
    }
}