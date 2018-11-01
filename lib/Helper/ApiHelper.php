<?php

namespace Smartcat\Connector\Helper;

class ApiHelper
{
    protected static $api = NULL;

    public static function createApi()
    {
        if(self::$api === NULL){
            $apiId = \Bitrix\Main\Config\Option::get('smartcat.connector', 'api_id');
            $apiSecret = \Bitrix\Main\Config\Option::get('smartcat.connector', 'api_secret');
            $apiServer = \Bitrix\Main\Config\Option::get('smartcat.connector', 'api_server');

            self::$api = new \SmartCat\Client\SmartCat($apiId, $apiSecret, $apiServer);
        }
    
        return self::$api;
    }

    public static function getAccount()
    {
        return self::createApi()->getAccountManager()->accountGetAccountInfo();
    }

    public static function getDirectory($type)
    {
        return self::createApi()->getDirectoriesManager()->directoriesGet(['type'=>$type]);
    } 

    public static function getLanguages()
    {
        return self::getDirectory('language')->getItems();
    }

    public static function getServiceTypes()
    {
        $types = [];
        $items = self::getDirectory('lspServiceType')->getItems();
        foreach ($items as $type){
            $types[$type->getId()] = $type->getName();
        }
        return $types;
    }

    public static function getVendor()
    {
        $vendors = [];
        $items = self::getDirectory('vendor')->getItems();
        foreach ($items as $vendor){
            $vendors[$vendor->getId()] = $vendor->getName();
        }
        return $vendors;
    }

    public static function getWorkflowStages()
    {
        return Array("translation","editing","proofreading","postediting");
    }

}
