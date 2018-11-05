<?php

namespace Smartcat\Connector\Helper;

use \Smartcat\Connector\Helper\ApiHelper;

class ApiDirectoryHelper
{
    protected static $directoris = [];

    public static function getDirectory($type)
    {
        if(!array_key_exists($type, self::$directoris)){
            self::$directoris[$type] = ApiHelper::createApi()->getDirectoriesManager()->directoriesGet(['type'=>$type]);
        }
        return self::$directoris[$type];
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