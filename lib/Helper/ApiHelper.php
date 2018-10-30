<?php

namespace Smartcat\Connector\Helper;

class ApiHelper
{
    public static function createApi(){
        $apiId = \Bitrix\Main\Config\Option::get('smartcat.connector', 'api_id');
        $apiSecret = \Bitrix\Main\Config\Option::get('smartcat.connector', 'api_secret');
        $apiServer = \Bitrix\Main\Config\Option::get('smartcat.connector', 'api_server');
    
        return new \SmartCat\Client\SmartCat($apiId, $apiSecret, $apiServer);
    }

    public static function getAccount(){
        return self::createApi()->getAccountManager()->accountGetAccountInfo();
    }
}
