<?php

namespace Smartcat\Connector\Helper;

class ApiHelper
{
    public static function createApi(){
        $apiId = \Bitrix\Main\Config\Option::get('smartcat.connector', 'api_id');
        $apiSecret = \Bitrix\Main\Config\Option::get('smartcat.connector', 'api_secret');
        $apiServer = \Bitrix\Main\Config\Option::get('smartcat.connector', 'api_server');
    
        return new \SmartCAT\API\SmartCAT($apiId, $apiSecret,$apiServer);
    }
}