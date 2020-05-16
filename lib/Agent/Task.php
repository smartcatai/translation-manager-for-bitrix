<?php

namespace Smartcat\Connector\Agent;

use CModule;
use SmartCat\Client\Model\BilingualFileImportSettingsModel;
use SmartCat\Client\Model\CreateDocumentPropertyWithFilesModel;
use SmartCat\Client\Model\DocumentModel;
use SmartCat\Client\Model\ProjectModel;
use SmartCat\Client\Model\UploadDocumentPropertiesModel;
use Smartcat\Connector\Helper\IblockHelper;
use Smartcat\Connector\Helper\LoggerHelper;
use Smartcat\Connector\Helper\StringHelper;
use Smartcat\Connector\Helper\ProjectHelper;
use Smartcat\Connector\ProfileIblockTable;
use Smartcat\Connector\ProfileTable;
use Smartcat\Connector\TaskFileTable;
use Smartcat\Connector\TaskTable;
use Smartcat\Connector\Helper\ApiHelper;
use Bitrix\Main\Loader;
use Bitrix\Main\Type\DateTime;

class Task
{
    const FILENAME = "Translation-";
    public static function Check()
    {
        self::log('Start checking');

        $schema = new \Smartcat\Connector\Schema(dirname(__FILE__) . '/../../install/db/mysql');

        if ($schema->needUpgrade()) {
            LoggerHelper::error('agent.ERROR', 'Database schema need to upgrade');

            self::log('End checking');

            return '\\' . __METHOD__ . '();';
        }

        self::CheckReadyTasks();
        self::CheckCanceledTasks();
        self::CheckUploadedTasks();
        self::CheckDocumentStatus();
        self::CheckExportStatus();
        self::CheckTaskFileSuccess();

        self::log('End checking');

        return '\\' . __METHOD__ . '();';
    }

    public static function CheckReadyTasks()
    {
        self::log("Starting CheckReadyTasks()");
        $projectsList = TaskTable::getList([
            'select' => ['PROJECT_ID'],
            'filter' => [
                '=STATUS' => TaskTable::STATUS_READY_UPLOAD,
            ]
        ]);

        $projectIds = [];
        foreach ($projectsList as $item) {
            array_push($projectIds, $item['PROJECT_ID']);
        }
        $projectIds = array_unique($projectIds);
        self::log("CheckReadyTasks got " . count($projectIds) . " projects to process");

        $api = ApiHelper::createApi();
        $projectManager = $api->getProjectManager();
        $documentManager = $api->getDocumentManager();

        foreach ($projectIds as $projectId) {
            self::log("Processing Project: {$projectId}");
            // проверяем externalTag и меняем если нужно
            try {
                $project = ApiHelper::getProject($projectId);
                if (!empty($project) && $project->getExternalTag() !== 'source:Bitrix') {
                    $project = ApiHelper::updateProjectExternalTag($projectId);
                    self::log("Updated Project {$projectId} externalTag");
                }
            } catch(\Exception $e) {
                self::errorHandler($e);
            }

            try {
                self::log("Fetching project documents from DB");
                $rsTasks = TaskTable::getList([
                    'order' => ['ID' => 'asc'],
                    'filter' => [
                        '=STATUS' => TaskTable::STATUS_READY_UPLOAD,
                        '=PROJECT_ID' => $projectId
                    ]
                ]);
            } catch(\Exception $e) {
                self::errorHandler($e);
            }
            self::log("Got {$rsTasks->getSelectedRowsCount()} documents to process");
            try {
                $project = $projectManager->projectGet($projectId);
                $scProjectDocuments = $project->getDocuments();
                $chunkSize = 20;
                $chunksCount = ceil($rsTasks->getSelectedRowsCount() / $chunkSize);
                $arTasks = $rsTasks->fetchAll();

                for ($i = 0; $i < $chunksCount; $i++) {
                    self::log("Processing batch #" . ($i + 1));
                    $batchTasks = array_slice($arTasks, $chunkSize * $i, $chunkSize);

                    $documentsForUpdate = [];
                    $documentsForCreate = [];

                    foreach ($batchTasks as $arTask) {
                        $documentFilename = self::FILENAME . $arTask['ID'];
                        self::log("Processing document " . $documentFilename);
                        $sFilePath = tempnam(sys_get_temp_dir(), 'TRANSLATE-');
                        file_put_contents($sFilePath, '<html><head></head><body>' . $arTask['CONTENT'] . '</body></html>');
                        $document = ProjectHelper::createDocumentFromFile($sFilePath, $documentFilename . '.html');
                        self::log("File for document " . $documentFilename . " created");

                        $foundId = '';
                        foreach ($scProjectDocuments as $scProjectDocument) {
                            if ($scProjectDocument->getName() === $documentFilename) {
                                $foundId = $scProjectDocument->getId();
                                break;
                            }
                        }
                        if ($foundId !== '') {
                            $forUpdate = [
                                'documentId' => $foundId,
                                'uploadedFile' => $document->getFile(),
                            ];
                            array_push($documentsForUpdate, $forUpdate);
                        } else {
                            array_push($documentsForCreate, $document);
                        }
                    }

                    self::log("Starting batch document upload");
                    $documentsCreated = $projectManager->projectAddDocument([
                        'documentModel' => $documentsForCreate,
                        'projectId' => $projectId,
                    ]);
                    self::UpdateUploadedDocumentsStatuses($documentsCreated);

                    self::log("Starting batch document update");
                    foreach ($documentsForUpdate as $documentForUpdate) {
                        $updatedDocument = $documentManager->documentUpdate($documentForUpdate);
                        self::UpdateUploadedDocumentsStatuses($updatedDocument);
                    }
                }
            } catch(\Exception $e) {
                self::errorHandler($e);
                continue;
            }
        }
    }

    public static function UpdateUploadedDocumentsStatuses($documents)
    {
        foreach ($documents as $document) {
            preg_match('/' .self::FILENAME . '(\d+)/', $document->getName(), $matches);
            $taskId = (int)$matches[1];

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
                    self::log("Document {$document->getId()} added to project");
                }
            }
        }
        self::log("Document chunk processed");
    }

    public static function CheckUploadedTasks()
    {
        $rsTasks = TaskTable::getList([
            'order' => ['ID' => 'asc'],
            'filter' => [
                '=STATUS' => TaskTable::STATUS_UPLOADED,
            ]
        ]);
        $api = ApiHelper::createApi();
        $projectManager = $api->getProjectManager();

        self::log("Check uploaded: {$rsTasks->getSelectedRowsCount()}");

        if ($rsTasks->getSelectedRowsCount() === 0) {
            return;
        }

        while ($arTask = $rsTasks->fetch()) {
            try {
                $project = $projectManager->projectGet($arTask['PROJECT_ID']);
            } catch (\Exception $e) {
                self::errorHandler($e);
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

            if ($disasemblingSuccess && $arTask['STATS_BUILDED'] === 'N') {
                try {
                    $projectManager->projectBuildStatistics($project->getId());
                    TaskTable::update($arTask['ID'], [
                        'STATS_BUILDED' => 'Y'
                    ]);
                } catch (\Exception $e) {
                    self::errorHandler($e);
                }
            }

            if ($project) {
                if (strtolower($project->getStatus()) == 'inprogress') {
                    TaskTable::update($arTask['ID'], [
                        'STATUS' => TaskTable::STATUS_PROCESS,
                        'DEADLINE' => $project->getDeadline() instanceof \DateTime ? DateTime::createFromTimestamp($project->getDeadline()->getTimestamp()) : $arTask['DEADLINE']
                    ]);

                    self::log("Set to project id {$arTask['ID']} status 'In Progress'");
                }

                if (strtolower($project->getStatus()) == 'canceled') {
                    TaskTable::update($arTask['ID'], [
                        'STATUS' => TaskTable::STATUS_CANCELED,
                    ]);

                    self::log("Set to project id {$arTask['ID']} status 'Canceled'");
                }
            }
        }
    }

    public static function CheckCanceledTasks()
    {
        $rsTasks = TaskTable::getList([
            'order' => ['ID' => 'asc'],
            'filter' => [
                '=STATUS' => TaskTable::STATUS_PROCESS,
            ]
        ]);

        self::log("Check for canceled tasks: {$rsTasks->getSelectedRowsCount()}");

        $api = ApiHelper::createApi();
        $projectManager = $api->getProjectManager();

        while ($arTask = $rsTasks->fetch()) {
            try {
                $project = $projectManager->projectGet($arTask['PROJECT_ID']);
                // Check if failed documents deleted from Smartcat
                $rsTaskFiles = TaskFileTable::getList([
                    'order' => ['ID' => 'asc'],
                    'filter' => [
                        '=STATUS' => TaskFileTable::STATUS_FAILED,
                        '=TASK_ID' => $arTask["ID"]
                    ]
                ]);
                $projectDocuments = $project->getDocuments();
                $projectDocumentIds = [];
                foreach ($projectDocuments as $projectDocument) {
                    array_push($projectDocumentIds, $projectDocument->getId());
                }
                while ($arTaskFile = $rsTaskFiles->fetch()) {
                    if (!in_array($arTaskFile["DOCUMENT_ID"], $projectDocumentIds)) {
                        TaskTable::update($arTask['ID'], [
                            'STATUS' => TaskTable::STATUS_FAILED,
                            'COMMENT' => 'Not found'
                        ]);
                    }
                }
            } catch (\Exception $e) {
                self::errorHandler($e);
                TaskTable::update($arTask['ID'], [
                    'STATUS' => TaskTable::STATUS_FAILED,
                    'COMMENT' => $e->getMessage()
                ]);
                continue;
            }
            if ($project) {
                if (strtolower($project->getStatus()) == 'canceled') {
                    TaskTable::update($arTask['ID'], [
                        'STATUS' => TaskTable::STATUS_CANCELED,
                    ]);
                    self::log("Set to project id {$arTask['ID']} status 'Canceled'");
                } else {
                    TaskTable::update($arTask['ID'], [
                        'DEADLINE' => $project->getDeadline() instanceof \DateTime ? DateTime::createFromTimestamp($project->getDeadline()->getTimestamp()) : $arTask['DEADLINE']
                    ]);
                }
            }
        }
    }

    public static function CheckDocumentStatus()
    {
        $rsTaskFiles = TaskFileTable::getList([
            'order' => ['ID' => 'asc'],
            'filter' => [
                '=STATUS' => TaskFileTable::STATUS_UPLOADED,
            ]
        ]);

        self::log("Check documents status: {$rsTaskFiles->getSelectedRowsCount()}");

        if ($rsTaskFiles->getSelectedRowsCount() === 0) {
            return;
        }

        $api = ApiHelper::createApi();
        $documentManager = $api->getDocumentManager();
        $documentExportManager = $api->getDocumentExportManager();

        while ($arTaskFile = $rsTaskFiles->fetch()) {
            try {
                $document = $documentManager->documentGet(['documentId'=>$arTaskFile['DOCUMENT_ID']]);

                if ($document) {
                    if ($document->getStatus() !== 'completed') {
                        continue;
                    }

                    $export = $documentExportManager->documentExportRequestExport(['documentIds'=>[$document->getId()]]);

                    TaskFileTable::update($arTaskFile['ID'], [
                        'EXPORT_TASK_ID' => $export->getId(),
                        'STATUS' => TaskFileTable::STATUS_PROCESS,
                    ]);

                    self::log("Request export for document {$arTaskFile['DOCUMENT_ID']}");
                }
            } catch (\Http\Client\Exception\HttpException $e) {
                self::errorHandler($e);
                if ($e->getResponse()->getStatusCode() === 404) {
                    TaskFileTable::update($arTaskFile['ID'], [
                        'STATUS' => TaskFileTable::STATUS_FAILED,
                    ]);
                }
            } catch (\Exception $e) {
                self::errorHandler($e);
            }
        }
    }

    public static function CheckExportStatus()
    {
        $rsTaskFiles = TaskFileTable::getList([
            'order' => ['ID' => 'asc'],
            'filter' => [
                '=STATUS' => TaskFileTable::STATUS_PROCESS,
            ]
        ]);

        self::log("Check documents export status: {$rsTaskFiles->getSelectedRowsCount()}");

        if ($rsTaskFiles->getSelectedRowsCount() === 0) {
            return;
        }

        $api = ApiHelper::createApi();
        $documentExportManager = $api->getDocumentExportManager();
        $exportUnpacked = [];

        while ($arTaskFile = $rsTaskFiles->fetch()) {
            if (in_array($arTaskFile['EXPORT_TASK_ID'], $exportUnpacked)) {
                continue;
            }

            array_push($exportUnpacked, $arTaskFile['EXPORT_TASK_ID']);

            try {
                $response = $documentExportManager->documentExportDownloadExportResult($arTaskFile['EXPORT_TASK_ID']);
            } catch (\Exception $e) {
                self::errorHandler($e);
                TaskFileTable::update($arTaskFile['ID'], [
                    'STATUS' => TaskFileTable::STATUS_UPLOADED,
                ]);
                return;
            }

            if (!$response) {
                return;
            }

            $mimeType = $response->getHeaderLine('Content-Type');

            if ($response->getStatusCode() === 204) {
                continue;
            }

            self::log("Processing downloaded file for: {$arTaskFile['DOCUMENT_ID']}");

            if ($mimeType === 'text/html') {
                $name = sys_get_temp_dir() . '/' . self::FILENAME . $arTaskFile['TASK_ID'] . '(' . $arTaskFile['LANG_TO'] . ').html';
                file_put_contents( $name , $response->getBody()->getContents());
                TaskFileTable::update($arTaskFile['ID'], [
                    'STATUS' => TaskFileTable::STATUS_SUCCESS,
                ]);
                continue;
            }

            $sFilePath = tempnam(sys_get_temp_dir(), "EXPORT-{$arTaskFile['EXPORT_TASK_ID']}-") . '.zip';
            file_put_contents($sFilePath, $response->getBody()->getContents());
			$arc = \CBXArchive::GetArchive($sFilePath);

			if ($arc instanceof IBXArchive) {
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

                if (!$uRes) {
                    self::log($arc->GetErrors());
                } else {
                    TaskFileTable::update($arTaskFile['ID'], [
                        'STATUS' => TaskFileTable::STATUS_SUCCESS,
                    ]);

                    self::log("Archive was unpacked for: {$arTaskFile['DOCUMENT_ID']}");
                }
            } else {
                self::log("ARC is not IBXArchive", get_class($arc));
            }
        }
    }

    public static function CheckTaskFileSuccess()
    {
        $rsTaskFiles = TaskFileTable::getList([
            'order' => ['ID' => 'asc'],
            'filter' => [
                '=STATUS' => TaskFileTable::STATUS_SUCCESS,
            ]
        ]);

        self::log("Check task file success: {$rsTaskFiles->getSelectedRowsCount()}");

        if ($rsTaskFiles->getSelectedRowsCount() === 0) {
            return ;
        }

        if(!CModule::IncludeModule('iblock'))
            return;

        $CIBlockElement = new \CIBlockElement();
    
        while ($arTaskFile = $rsTaskFiles->fetch()) {
            self::log("Create block for: {$arTaskFile['DOCUMENT_ID']}");

            $name = sys_get_temp_dir() .'/' . self::FILENAME.$arTaskFile['TASK_ID'].'('.$arTaskFile['LANG_TO'].').html';
            $translateText = file_get_contents($name);

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

            $arTask = TaskTable::getList([
                'order' => ['ID' => 'asc'],
                'filter' => [
                    '=ID' => $arTaskFile['TASK_ID'],
                ]
            ])->fetch();

            $arProfileIblock = ProfileIblockTable::getList([
                'filter' => [
                    '=PROFILE_ID' => $arTask['PROFILE_ID'],
                    '=LANG' => $arTaskFile['LANG_TO'],
                ],
            ])->fetch();

            $arProfile = ProfileTable::getList([
                'filter' => [
                    '=ID' => $arTask['PROFILE_ID'],
                ],
            ])->fetch();

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
                self::errorHandler($e);
                $sErrorComment = $e->getMessage();
                $bHasErrors = true;
            }

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

            if ($CIBlockElement->LAST_ERROR) {
                self::log('IB Error', $CIBlockElement->LAST_ERROR, $arElement);
            }

            if ($elementID > 0) {
                TaskFileTable::update($arTaskFile['ID'], [
                    'TRANSLATION' => $translateText,
                    'STATUS' => TaskFileTable::STATUS_DONE,
                    'ELEMENT_ID' => $elementID,
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
                                } else {
                                    self::log("Block created {$arElement['IBLOCK_SECTION_ID']}");
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
                self::log("Unknown error");
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

        LoggerHelper::debug('agent.LOG', $mess);
    }

    private static function errorHandler(\Throwable $e) {
        if ($e instanceof \Http\Client\Exception\HttpException) {
            $body = $e->getResponse()->getBody()->getContents();

            if ($e->getResponse()->getStatusCode() === 401) {
                $body = 'Smartcat credentials are incorrect';
            }

            $msg = "API Error: {$e->getResponse()->getStatusCode()} {$body}";
        } else {
            $msg = "System Error: {$e->getCode()} {$e->getMessage()}";
        }

        LoggerHelper::debug('agent.ERROR', $msg);
    }
}