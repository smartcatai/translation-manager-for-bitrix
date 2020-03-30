<?php

namespace Smartcat\Connector\Helper;

use SmartCat\Client\Model\ProjectChangesModel;
use Smartcat\Connector\TaskTable;
use SmartCat\Client\SmartCat;
use Bitrix\Main\Config\Option;

class ApiHelper
{
    const DEFAULT_WORKFLOW_STAGE = "translation";
    protected static $api = NULL;

    public static function createApi()
    {
        if(self::$api === NULL){
            $apiId = Option::get('smartcat.connector', 'api_id');
            $apiSecret = Option::get('smartcat.connector', 'api_secret');
            $apiServer = Option::get('smartcat.connector', 'api_server');

            self::$api = new SmartCat($apiId, $apiSecret, $apiServer);
        }
    
        return self::$api;
    }

    public static function checkAccountApi($apiId, $apiSecret, $apiServer)
    {
        $api = new SmartCat($apiId, $apiSecret, $apiServer);
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

    public static function getProject($projectId)
    {
        try {
            $project = self::createApi()
                ->getProjectManager()
                ->projectGet($projectId);
        } catch (\Exception $e) {
            LoggerHelper::error('helper.apihelper', $e->getMessage());
            return null;
        }

        return $project;
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
            LoggerHelper::error('helper.apihelper', $msg);
            return [
                'STATUS' => TaskTable::STATUS_FAILED,
                'COMMENT' => $msg,
            ];
        }
        return $project;
    }

    public static function updateProjectExternalTag($projectId)
    {
        $existingProject = self::getProject($projectId);
        $projectUpdateModel = (new ProjectChangesModel())
            ->setName($existingProject->getName())
            ->setExternalTag('source:Bitrix');

        try {
            $project = self::createApi()
                ->getProjectManager()
                ->projectUpdateProject($projectId, $projectUpdateModel);
        } catch (\Exception $e) {
            $msg = $e->getMessage();
            if($e instanceof \Http\Client\Common\Exception\ClientErrorException ){
                $msg .= ' ' . $e->getResponse()->getBody()->getContents();
            }
            LoggerHelper::error('helper.apihelper', $msg);
            return [
                'STATUS' => TaskTable::STATUS_FAILED,
                'COMMENT' => $msg,
            ];
        }
        return $project;
    }

}
