<?php

namespace Smartcat\Connector\Helper;


use Bitrix\Main\Loader;
use Smartcat\Connector\ProfileIblockTable;
use Smartcat\Connector\ProfileTable;
use Smartcat\Connector\TaskFileTable;
use Smartcat\Connector\TaskTable;

use SmartCat\Client\Model\BilingualFileImportSettingsModel;
use SmartCat\Client\Model\CreateDocumentPropertyWithFilesModel;
use SmartCat\Client\Model\CreateProjectModel;
use SmartCat\Client\Model\CreateProjectWithFilesModel;
use SmartCat\Client\Model\ProjectChangesModel;
use Bitrix\Main\Type\DateTime;

class ProjectHelper
{
    public static function prepareProjectParams($arProfile, $name)
    {
        $rsIBlocks = ProfileIblockTable::getList([
            'filter' => [
                '=PROFILE_ID' => $arProfile['ID'],
            ],
        ]);

        $arIBlocks = $rsIBlocks->fetchAll();

        $arLangs = [];
        foreach ($arIBlocks as $arIBlock) {
            $arLangs[] = $arIBlock['LANG'];
        }

        $vendorId = NULL;
        if(strstr($arProfile[VENDOR], '|', true)!=='0'){
            $vendorId = [strstr($arProfile[VENDOR], '|', true)];
        }

        return Array(
            'name' => mb_substr(str_replace(['*','|','\\',':','"','<','>','?','/'], ' ', $name),0,70),
            'desc' => 'Content from bitrix module',
            'source_lang' => $arProfile['LANG'],
            'target_langs' => $arLangs,
            'stages' => explode(',', $arProfile['WORKFLOW']),
            'test' => false,
            'vendorId' => $vendorId,
            'external_tag' => 'source:Bitrix',
        );
    }

    public static function createProjectWithFile($arProfile, $arTask, $obElement, $arFile)
    {
        $params = self::prepareProjectParams($arProfile, $arTask, $obElement);

        $project = new CreateProjectWithFilesModel();
        $project
            ->setName($params['name'])
            ->setDescription($params['desc'])
            ->setSourceLanguage($params['source_lang'])
            ->setTargetLanguages($params['target_langs'])
            ->setUseMT(false)
            ->setPretranslate(false)
            ->setWorkflowStages($params['stages'])
            ->setAssignToVendor(false)
            ->setExternalTag($params['external_tag'])
            ->setIsForTesting($params['test'])
            ->attacheFile(fopen($arFile['path']),$arFile['name']);

        return $project;
    }


    public static function createProject($params)
    {
        $project = (new CreateProjectModel())
            ->setName($params['name'])
            ->setDescription($params['desc'])
            ->setSourceLanguage($params['source_lang'])
            ->setTargetLanguages($params['target_langs'])
            ->setUseMT(false)
            ->setPretranslate(false)
            ->setWorkflowStages($params['stages'])
            ->setAssignToVendor(false)
            ->setExternalTag($params['external_tag'])
            ->setIsForTesting($params['test']);

        if(!empty($params['vendorId'])){
            $project
                ->setAssignToVendor(true)
                ->setVendorAccountIds($params['vendorId']);
        }
        return $project;
    }

    public static function getFileImportSettings()
    {
        return (new BilingualFileImportSettingsModel())
            ->setConfirmMode('none')
            ->setLockMode('none')
            ->setTargetSubstitutionMode('all');
    }

    public static function createDocumentFromFile($filePath, $fileName)
    {
        $documentModel = new CreateDocumentPropertyWithFilesModel();
        $documentModel->setBilingualFileImportSettings(self::getFileImportSettings());
        $documentModel->attachFile($filePath, $fileName);
        return $documentModel;
    }

    public static function createVendorChange($vendor)
    {
        $vendorId = strstr($vendor, '|', true);
        return (new ProjectChangesModel())
            ->setVendorAccountIds([$vendorId]);
    }
}