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

    public static function getLanguages()
    {
        return ApiDirectoryHelper::getDirectory('language')->getItems();
    }

    public static function getServiceTypes()
    {
        return ApiDirectoryHelper::getItemsAsArray('lspServiceType');
    }

    public static function getVendor()
    {
        return ApiDirectoryHelper::getItemsAsArray('vendor');
    }

    public static function getProjectStatus()
    {
        return ApiDirectoryHelper::getItemsAsArray('projectStatus');
    }

    public static function getWorkflowStages()
    {
        return Array("translation","editing","proofreading","postediting");
    }

}
