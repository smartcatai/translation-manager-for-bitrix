<?php
/**
 * Project: likee.smartcat
 * Date: 07.06.17
 *
 * @author: Timokhin Maxim <tm@likee.ru>
 */

namespace Abbyy\Cloud\Helper;


class StringHelper
{


    public static function specialcharsDecode($str)
    {
        $str = html_entity_decode($str);
        $str = str_replace('&#39;', "'", $str);
        return $str;
    }
}