<?php

namespace Abbyy\Cloud\Helper;


class ArrayHelper
{

    public static function cleanUpTilda($ar)
    {
        foreach ($ar as $k => $value) {
            if (is_array($value)) {
                $ar[$k] = static::cleanUpTilda($value);
            } else {
                if ($k[0] == '~') {
                    unset($ar[$k]);
                }
            }
        }
        return $ar;
    }

}