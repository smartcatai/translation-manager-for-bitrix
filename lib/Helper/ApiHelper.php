<?php

namespace Smartcat\Connector\Helper;

use Smartcat\Connector\Helper\ProjectHelper;
use Smartcat\Connector\TaskTable;

class ApiHelper
{
    const DEFAULT_WORKFLOW_STAGE = "translation";
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

    public static function checkAccountApi($apiId, $apiSecret, $apiServer)
    {
        $api = new \SmartCat\Client\SmartCat($apiId, $apiSecret, $apiServer);
        return $api->getAccountManager()->accountGetAccountInfo();
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
        return Array(
            "translation"=>GetMessage("SMARTCAT_CONNECTOR_STAGE_TRANSLATION"),
            "editing"=>GetMessage("SMARTCAT_CONNECTOR_STAGE_EDITING"),
            "proofreading"=>GetMessage("SMARTCAT_CONNECTOR_STAGE_PROOFREADING"),
        );
    }

    public static function createProject($arProfile, $name)
    {
        $project = NULL;
        $params = ProjectHelper::prepareProjectParams($arProfile, $name);

        try{
            $project = self::createApi()
                ->getProjectManager()
                ->projectCreateProject(ProjectHelper::createProject($params));
        } catch (\Exception $e) {
            $msg = $e->getMessage();
            if($e instanceof \Http\Client\Common\Exception\ClientErrorException ){
                $msg .= ' ' . $e->getResponse()->getBody()->getContents();
            }
            return [
                'STATUS' => TaskTable::STATUS_FAILED,
                'COMMENT' => $msg,
            ];
        }
        return $project;
    }

}
