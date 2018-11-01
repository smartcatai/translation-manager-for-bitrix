<?php

namespace Smartcat\Connector\Helper;

use \Smartcat\Connector\Helper\ApiHelper;

class ApiDirectoryHelper
{
    public static function getDirectory($type)
    {
        return ApiHelper::createApi()->getDirectoriesManager()->directoriesGet(['type'=>$type]);
    }

    public static function getItemsAsArray($type)
    {
        $array = [];
        $items = self::getDirectory($type)->getItems();
        foreach ($items as $item){
            $array[$item->getId()] = $item->getName();
        }
        return $array;
    }

}