<?php

namespace Smartcat\Connector\Agent;

use SmartCat\Client\Model\ProjectModel;
use Smartcat\Connector\Helper\IblockHelper;
use Smartcat\Connector\Helper\StringHelper;
use Smartcat\Connector\Helper\ProjectHelper;
use Smartcat\Connector\ProfileIblockTable;
use Smartcat\Connector\ProfileTable;
use Smartcat\Connector\TaskFileTable;
use Smartcat\Connector\TaskTable;
use Bitrix\Main\Loader;
use Bitrix\Main\Type\DateTime;

class Task
{
    const FILENAME = "Translation-";
    public static function Check()
    {
        self::log("Start Check");

        self::log("Start CheckReadyTasks");
        self::CheckReadyTasks();

        self::log("Start CheckUploadedTasks");
        self::CheckUploadedTasks();

        self::log("Start CheckDocumentStatus");
        self::CheckDocumentStatus();

        self::log("Start CheckExportStatus");
        self::CheckExportStatus();

        self::log("Start CheckTaskFileSuccess");
        self::CheckTaskFileSuccess();

        self::log("Done");
        return '\\' . __METHOD__ . '();';
    }

    public static function CheckReadyTasks()
    {
        $rsTasks = TaskTable::getList([
            'order' => ['ID' => 'asc'],
            'filter' => [
                '=STATUS' => TaskTable::STATUS_READY_UPLOAD,
            ]
        ]);

        if ($rsTasks->getSelectedRowsCount() === 0) {
            self::log("End CheckReadyTasks 0");
            return;
        }

        $api = \Smartcat\Connector\Helper\ApiHelper::createApi();
        $projectManager = $api->getProjectManager();
        $projectDocuments = [];

        while ($arTask = $rsTasks->fetch()) {

            $projectId = $arTask['PROJECT_ID'];

            if(empty($projectId)){
                $arProfile = ProfileTable::getById($arTask['PROFILE_ID'])->fetch();
                $arElement = \CIBlockElement::GetByID($arTask['ELEMENT_ID'])->GetNextElement(true, false)->GetFields();
                try{
                    $project = \Smartcat\Connector\Helper\ApiHelper::createProject($arProfile, $arElement['NAME']);
                    $projectId = $project->getId();
                    TaskTable::update($arTask['ID'], [
                        'PROJECT_ID' => $project->getId(),
                        'PROJECT_NAME' => $project->getName(),
                    ]);
                }catch(\Http\Client\Common\Exception\ClientErrorException $e){
                    self::log("SmartCat error create project: {$e->getMessage()}");
                    continue;
                }
            }

            if(!array_key_exists($projectId,$projectDocuments)){
                $projectDocuments[$projectId] = [];
            }

            $sFilePath = tempnam(sys_get_temp_dir(), 'TRANSLATE-');

            file_put_contents($sFilePath, '<html><head></head><body>' . $arTask['CONTENT'] . '</body></html>');

            $projectDocuments[$projectId][] = ProjectHelper::createDocumentFromFile($sFilePath, self::FILENAME . $arTask['ID'] . '.html');
        }

        foreach($projectDocuments as $projectId => $documentModels){
            try{
                $documents = $projectManager->projectAddDocument([
                    'documentModel' => $documentModels,
                    'projectId' => $projectId,
                ]);
            }catch(\Exception $e){
                self::log("SmartCat error add documents: {$e->getMessage()}");
                return;
            }

            if (!empty($documents)) {
                self::log("SmartCat documents work");
                foreach($documents as $document){
                    preg_match('/' .self::FILENAME . '(\d+)/', $document->getName(), $matches);
                    $taskId = (int)$matches[1];
                    self::log("SmartCat document task id: {$taskId}");
                    self::log("SmartCat document task id: {$matches[1]}");
                    self::log("SmartCat document task id: {$document->getName()}");
                    
                    TaskTable::update($taskId, [
                        'STATUS' => TaskTable::STATUS_UPLOADED,
                    ]);
                    $rsTaskFiles = TaskFileTable::getList([
                        'order' => ['ID' => 'asc'],
                        'filter' => [
                            '=TASK_ID' => $taskId,
                        ]
                    ]);
                    
                    while ($arTaskFile = $rsTaskFiles->fetch()) {
                        if($document->getTargetLanguage() === $arTaskFile['LANG_TO']){
                            TaskFileTable::update($arTaskFile['ID'], [
                                'DOCUMENT_ID' => $document->getId(),
                                'STATUS' => TaskFileTable::STATUS_UPLOADED,
                            ]);
                        }
                    }
                }
            }
        }
    }

    public static function CheckUploadedTasks()
    {
        $rsTasks = TaskTable::getList([
            'order' => ['ID' => 'asc'],
            'filter' => [
                '=STATUS' => TaskTable::STATUS_UPLOADED,
            ]
        ]);
        $api = \Smartcat\Connector\Helper\ApiHelper::createApi();
        $projectManager = $api->getProjectManager();

        if ($rsTasks->getSelectedRowsCount() === 0) {
            self::log("End CheckUploadedTasks 0");
            retrun;
        }

        while ($arTask = $rsTasks->fetch()) {
            try {
                $project = $projectManager->projectGet($arTask['PROJECT_ID']);
            } catch (\Exception $e) {
                self::log($e->getMessage() , __METHOD__, __LINE__);
                TaskTable::update($arTask['ID'], [
                    'STATUS' => TaskTable::STATUS_FAILED,
                    'COMMENT' => $e->getMessage()
                ]);
                continue;
            }

            $disasemblingSuccess = true;
            foreach($project->getDocuments() as $document){
                if($document->getDocumentDisassemblingStatus() != 'success'){
                    $disasemblingSuccess = false;
                    break;
                }
            }

            if($disasemblingSuccess){
                try{
                    $projectManager->projectBuildStatistics($project->getId());
                }catch(\Exception $e){
                    self::log("SmartCat error Build Statistics: {$e->getMessage()}");
                }
            }

            if ($project && strtolower($project->getStatus()) == 'inprogress') {
                TaskTable::update($arTask['ID'], [
                    'STATUS' => TaskTable::STATUS_PROCESS,
                    'DEADLINE' => $project->getDeadline() instanceof \DateTime ? DateTime::createFromTimestamp($project->getDeadline()->getTimestamp()) : $arTask['DEADLINE']
                ]);
            }
        }
        
    }

    public static function CheckWaitingTasks()
    {
        $rsTasks = TaskTable::getList([
            'order' => ['ID' => 'asc'],
            'filter' => [
                '=STATUS' => TaskTable::STATUS_WAITING,
            ]
        ]);
    }

    public static function CheckDocumentStatus()
    {
        $rsTaskFiles = TaskFileTable::getList([
            'order' => ['ID' => 'asc'],
            'filter' => [
                '=STATUS' => TaskFileTable::STATUS_UPLOADED,
            ]
        ]);

        self::log("End CheckDocumentStatus ", $rsTaskFiles->getSelectedRowsCount());
        if ($rsTaskFiles->getSelectedRowsCount() === 0) {
            self::log("End CheckDocumentStatus 0");
            return ;
        }

        $api = \Smartcat\Connector\Helper\ApiHelper::createApi();
        $documentManager = $api->getDocumentManager();
        $documentExportManager = $api->getDocumentExportManager();

        while ($arTaskFile = $rsTaskFiles->fetch()) {
            self::log("Task", $arTaskFile['DOCUMENT_ID']);

            $document = $documentManager->documentGet(['documentId'=>$arTaskFile['DOCUMENT_ID']]);

            if ($document) {
                self::log('Current status:', $document->getStatus());
                if ($document->getStatus() !== 'completed') {
                    continue;
                }

                $export = $documentExportManager->documentExportRequestExport(['documentIds'=>[$document->getId()]]);

                TaskFileTable::update($arTaskFile['ID'], [
                    'EXPORT_TASK_ID' => $export->getId(),
                    'STATUS' => TaskFileTable::STATUS_PROCESS,
                ]);

            }

        }
    }

    public static function CheckExportStatus(){
    
        $rsTaskFiles = TaskFileTable::getList([
            'order' => ['ID' => 'asc'],
            'filter' => [
                '=STATUS' => TaskFileTable::STATUS_PROCESS,
            ]
        ]);

        if ($rsTaskFiles->getSelectedRowsCount() === 0) {
            return ;
        }

        $api = \Smartcat\Connector\Helper\ApiHelper::createApi();
        $documentExportManager = $api->getDocumentExportManager();
        $exportUnpacked = [];

        while ($arTaskFile = $rsTaskFiles->fetch()) {
            if(in_array($arTaskFile['EXPORT_TASK_ID'], $exportUnpacked )){
                continue;
            }
            array_push($exportUnpacked, $arTaskFile['EXPORT_TASK_ID']);
            try{
                $response = $documentExportManager->documentExportDownloadExportResult($arTaskFile['EXPORT_TASK_ID']);
            }catch(\Exception $e){
                self::log("SmartCat error Download Export {$arTaskFile['EXPORT_TASK_ID']}: {$e->getMessage()}");
                TaskFileTable::update($arTaskFile['ID'], [
                    'STATUS' => TaskFileTable::STATUS_UPLOADED,
                ]);
                return;
            }
            if(!$response){
                return;
            }
            $mimeType = $response->getHeaderLine('Content-Type');
            if($response->getStatusCode() === 204){
                continue;
            }
            self::log($response->getStatusCode(),$mimeType);
            if($mimeType==='text/html'){
                $name = sys_get_temp_dir() . '/' . self::FILENAME . $arTaskFile['TASK_ID'] . '(' . $arTaskFile['LANG_TO'] . ').html';
                self::log('File name', $name );
                file_put_contents( $name , $response->getBody()->getContents());
                TaskFileTable::update($arTaskFile['ID'], [
                    'STATUS' => TaskFileTable::STATUS_SUCCESS,
                ]);
                continue;
            }

            $sFilePath = tempnam(sys_get_temp_dir(), "EXPORT-{$arTaskFile['EXPORT_TASK_ID']}-") . '.zip';
            file_put_contents($sFilePath, $response->getBody()->getContents());

			$arc = \CBXArchive::GetArchive($sFilePath);

			if ($arc instanceof IBXArchive)
            {
                global $USER;

                $arc->SetOptions
                    (
                    array(
                        "REMOVE_PATH"		=> $sFilePath,
                        "UNPACK_REPLACE"	=> true,
                        "CHECK_PERMISSIONS" => false,
                        )
                    );

                $uRes = $arc->Unpack(sys_get_temp_dir());

                if (!$uRes){
                    self::log($arc->GetErrors());
                }else{
                    TaskFileTable::update($arTaskFile['ID'], [
                        'STATUS' => TaskFileTable::STATUS_SUCCESS,
                    ]);
                }
            }else{
                self::log(get_class($arc));
            }
        }
    }

    public static function CheckTaskFileSuccess(){
        $rsTaskFiles = TaskFileTable::getList([
            'order' => ['ID' => 'asc'],
            'filter' => [
                '=STATUS' => TaskFileTable::STATUS_SUCCESS,
            ]
        ]);

        if ($rsTaskFiles->getSelectedRowsCount() === 0) {
            return ;
        }

        $CIBlockElement = new \CIBlockElement();
    
        while ($arTaskFile = $rsTaskFiles->fetch()) {
            $name = sys_get_temp_dir() .'/' . self::FILENAME.$arTaskFile['TASK_ID'].'('.$arTaskFile['LANG_TO'].').html';
            self::log('File name', $name );
            $translateText = file_get_contents($name);

            self::log("Parse file content", $translateText);
            preg_match_all('/<field id="(.+?)">(.*?)<\/field>/is', $translateText, $matches);
            $arFields = [];
            $arProps = [];
            $arSections = [];
            foreach ($matches[1] as $i => $sField) {
                if (substr($sField, 0, 4) == 'PROP') {
                    $arProps[substr($sField, 5)] = html_entity_decode($matches[2][$i]);
                } elseif (substr($sField, 0, 17) == 'IBLOCK_SECTION_ID') {
                    $arSections[] = $matches[2][$i];
                } else {
                    $arFields[$sField] = StringHelper::specialcharsDecode($matches[2][$i]);
                }
            }
            self::log("End parse file content");

            $arTask = TaskTable::getList([
                'order' => ['ID' => 'asc'],
                'filter' => [
                    '=ID' => $arTaskFile['TASK_ID'],
                ]
            ])->fetch();
            self::log("task ID",$arTask['ID']);

            $arProfileIblock = ProfileIblockTable::getList([
                'filter' => [
                    '=PROFILE_ID' => $arTask['PROFILE_ID'],
                    '=LANG' => $arTaskFile['LANG_TO'],
                ],
            ])->fetch();
            self::log("profile iblock ID",$arProfileIblock['ID']);

            $arProfile = ProfileTable::getList([
                'filter' => [
                    '=ID' => $arTask['PROFILE_ID'],
                ],
            ])->fetch();
            self::log("Profile ID", $arProfile['ID']);

            $arElement = [
                'IBLOCK_ID' => $arProfileIblock['IBLOCK_ID'],
                'ACTIVE' => $arProfile['PUBLISH'] == 'Y' ? 'Y' : 'N',
                'PREVIEW_TEXT_TYPE' => 'html',
                'DETAIL_TEXT_TYPE' => 'html',
            ];
            IblockHelper::copyIBlockProps($arProfile['IBLOCK_ID'], $arProfileIblock['IBLOCK_ID']);
            $elementID = 0;
            try {
                $elementID = IblockHelper::copyElementToIB($arTask['ELEMENT_ID'], $arProfileIblock['IBLOCK_ID'], $arTaskFile['ELEMENT_ID']);
            } catch (\Exception $e) {
                if ($e->getCode() == IblockHelper::ERROR_LINKED_ELEMENT_NOT_FOUND) {
                    TaskTable::update($arTask['ID'], [
                        'STATUS' => TaskTable::STATUS_PROCESS,
                        'COMMENT' => $e->getMessage(),
                    ]);
                    $bWaiting = true;
                    continue;
                }
                self::log('Copy Error', $e->getMessage(), $arElement);
                $sErrorComment = $e->getMessage();
                $bHasErrors = true;
            }
            self::log($elementID);
            foreach ($arProfile['FIELDS']['FIELDS'] as $sField) {
                $arElement[$sField] = $arFields[$sField];
            }
            unset($arElement['IBLOCK_SECTION_ID']);
            if ($elementID > 0) {
                if($arElement['ACTIVE'] === 'Y'){
                    unset($arElement['ACTIVE']);
                }
                $CIBlockElement->Update($elementID, $arElement);
            } else {
                $elementID = $CIBlockElement->Add($arElement);
            }
            self::log($elementID);
            if ($CIBlockElement->LAST_ERROR) {
                self::log('IB Error', $CIBlockElement->LAST_ERROR, $arElement);
            }
            self::log("New element ID", $elementID);
            if ($elementID > 0) {
                TaskFileTable::update($arTaskFile['ID'], [
                    'TRANSLATION' => $translateText,
                    'STATUS' => TaskFileTable::STATUS_DONE,
                    'ELEMENT_ID' => $elementID,
                ]);
                TaskTable::update($arTask['ID'], [
                    'STATUS' => TaskFileTable::STATUS_SUCCESS,
                ]);
                \CIBlockElement::SetPropertyValuesEx($elementID, $arElement['IBLOCK_ID'], $arProps);
                if (!empty($arSections)) {
                    $CIBlockSection = new \CIBlockSection();
                    $arElement = \CIBlockElement::GetByID($elementID)->Fetch();
                    if ($arElement['IBLOCK_SECTION_ID'] > 0) {
                        $rsSections = \CIBlockSection::GetNavChain($arElement['IBLOCK_ID'], $arElement['IBLOCK_SECTION_ID'], ['ID', 'NAME', 'XML_ID']);
                        $i = 0;
                        while ($arSection = $rsSections->Fetch()) {
                            if (empty($arSection['XML_ID'])) continue;
                            if (!empty($arSections[$i])) {
                                $res = $CIBlockSection->Update($arSection['ID'], [
                                    'NAME' => StringHelper::specialcharsDecode(trim($arSections[$i])),
                                ]);
                                if (!$res) {
                                    self::log($CIBlockSection->LAST_ERROR, __LINE__);
                                }
                            }
                            $i++;
                        }
                    }
                }
            } else {
                TaskFileTable::update($arTaskFile['ID'], [
                    'STATUS' => TaskFileTable::STATUS_FAILED,
                ]);
            }
        }
    }

    public static function log()
    {
        $arMessage = func_get_args();
        $arOutput = [];
        foreach ($arMessage as $mess) {
            if (is_array($mess) || is_object($mess)) $mess = print_r($mess, true);
            $arOutput[] = $mess;
        }
        $mess = implode(', ', $arOutput) . PHP_EOL;
        //echo date('d.m.Y H:i:s') . ': ' . $mess;
        //fwrite(STDERR, date('d.m.Y H:i:s') . ': ' . $mess);
        file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/task_log.txt', date('d.m.Y H:i:s') . ': ' . $mess . "\n", FILE_APPEND);
    }

}