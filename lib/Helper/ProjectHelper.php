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
    public static function prepareProjectParams($arProfile, $arTask, $obElement)
    {
        $arElement = $obElement->GetFields();

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
        $name = $arElement['NAME'] ;//. '['. $arProfile['LANG'] .']:['.implode(',',$arLangs).']';

        return Array(
            'name' => $name,
            'desc' => 'test description',
            'source_lang' => $arProfile['LANG'],
            'target_langs' => $arLangs,
            'stages' => explode(',', $arProfile['WORKFLOW']),
        );
    }

    public static function createProjectWithFile($arProfile, $arTask, $obElement, $arFile)
    {
        $params = self::prepareProjectParams($arProfile, $arTask, $obElement);

        $project = new CreateProjectWithFilesModel();
        $project
            ->setName($params['name'])
            ->setDescription($params['desc'])
            ->setDeadline((new \DateTime('now'))->modify(' + 1 day'))
            ->setSourceLanguage($params['source_lang'])
            ->setTargetLanguages($params['target_langs'])
            ->setUseMT(false)
            ->setPretranslate(false)
            ->setWorkflowStages($params['stages'])
            ->setAssignToVendor(false)
            ->setIsForTesting(true)
            ->attacheFile(fopen($arFile['path']),$arFile['name']);

        return $project;
    }

    public static function createProject($arProfile, $arTask, $obElement)
    {
        $params = self::prepareProjectParams($arProfile, $arTask, $obElement);

        return (new CreateProjectModel())
            ->setName($params['name'])
            ->setDescription($params['desc'])
            ->setDeadline((new \DateTime('now'))->modify(' + 1 day'))
            ->setSourceLanguage($params['source_lang'])
            ->setTargetLanguages($params['target_langs'])
            ->setUseMT(false)
            ->setPretranslate(false)
            ->setWorkflowStages($params['stages'])
            ->setAssignToVendor(false)
            ->setIsForTesting(true);
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

    public static function createVendorChange($vendorId)
    {
        $projectChanges = (new ProjectChangesModel())
                ->setVendorAccountId($vendorId);
    }
}