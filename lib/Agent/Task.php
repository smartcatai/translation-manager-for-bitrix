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

    public static function Check()
    {
        self::log("Start Check");

        self::log("Start CheckNewTasks");
        self::CheckNewTasks();

        self::log("Start CheckUploadedTasks");
        //self::CheckUploadedTasks();

        self::log("Start CheckInProgressTasks");
        //self::CheckInProgressTasks();

        self::log("Done");
        return '\\' . __METHOD__ . '();';
    }

    public static function CheckNewTasks()
    {
        $rsTasks = TaskTable::getList([
            'order' => ['ID' => 'asc'],
            'filter' => [
                '=STATUS' => TaskTable::STATUS_NEW,
            ]
        ]);

        if ($rsTasks->getSelectedRowsCount() === 0) {
            return;
        }

        $api = \Smartcat\Connector\Helper\ApiHelper::createApi();
        $projectManager = $api->getProjectManager();

        while ($arTask = $rsTasks->fetch()) {

            $arProfile = ProfileTable::getById($arTask['PROFILE_ID'])->fetch();
            $obElement = \CIBlockElement::GetByID($arTask['ELEMENT_ID'])->GetNextElement(true, false);
            $newProject = ProjectHelper::createProject($arProfile, $arTask, $obElement);

            try {
                $project = $projectManager->projectCreateProject($newProject);
            }catch(\Exception $e){
                self::log("SmartCat error add project: {$e->getMessage()}");
                return;
            }

            if($project === null){
                return;
            }

            $sFilePath = tempnam(sys_get_temp_dir(), 'TRANSLATE-');

            file_put_contents($sFilePath, '<html><head></head><body>' . $arTask['CONTENT'] . '</body></html>');

            $documentModel = ProjectHelper::createDocumentFromFile($sFilePath, 'TRANSLATED-' . $arTask['ID'] . '.html');

            try{
                $documents = $projectManager->projectAddDocument([
                    'documentModel' => [$documentModel],
                    'projectId' => $project->getId(),
                ]);
            }catch(\Exception $e){
                self::log("SmartCat error add documents: {$e->getMessage()}");
                return;
            }

            $vendorId = substr($arProfile[VENDOR], 0, strpos('|'));

            if($vendorId != 0){
                $projectChanges = ProjectHelper::createVendorChange($vendorId);
                try{ 
                    $projectManager->projectUpdateProject($project->getId(), $projectChanges);
                }catch(\Exception $e){
                    self::log("SmartCat error add vendor {$vendorId}: {$e->getMessage()}");
                }
            }

            if (!empty($documents)) {
                TaskTable::update($arTask['ID'], [
                    'PROJECT_ID' => $project->getId(),
                    'PROJECT_NAME' => $project->getName(),
                    'STATUS' => TaskTable::STATUS_UPLOADED,
                ]);
                $rsTaskFiles = TaskFileTable::getList([
                    'order' => ['ID' => 'asc'],
                    'filter' => [
                        '=TASK_ID' => $arTask['ID'],
                    ]
                ]);
                
                while ($arTaskFile = $rsTaskFiles->fetch()) {
                    foreach($documents as $document){
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

        if ($rsTasks->getSelectedRowsCount() > 0) {

            while ($arTask = $rsTasks->fetch()) {
                try {
                    $project = $projectManager->projectGet($arTask['PROJECT_ID']);
                } catch (\Exception $e) {
                    self::log($e->getMessage() , __METHOD__, __LINE__);
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

                if ($project && $project instanceof ProjectModel && $project->getStatus() === 'inprogress') {
                    TaskTable::update($arTask['ID'], [
                        'STATUS' => TaskTable::STATUS_PROCESS,
                        'DEADLINE' => $project->getDeadline() instanceof \DateTime ? DateTime::createFromTimestamp($project->getDeadline()->getTimestamp()) : ''
                    ]);
                }
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

        if ($rsTaskFiles->getSelectedRowsCount() === 0) {
            return ;
        }

        $api = \Smartcat\Connector\Helper\ApiHelper::createApi();
        $documentManager = $api->getDocumentManager();
        $documentExportManager = $api->getDocumentExportManager();

        $CIBlockElement = new \CIBlockElement();

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

        while ($arTaskFile = $rsTaskFiles->fetch()) {
            $response = $documentExportManager->documentExportDownloadExportResult($arTaskFile['EXPORT_TASK_ID']);
            $sFilePath = tempnam(sys_get_temp_dir(), "EXPORT-{$arTaskFile['EXPORT_TASK_ID']}-");
            file_put_contents($sFilePath, $response->getBody());


			$arc = CBXArchive::GetArchive($sFilePath);

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

                $uRes = $arc->Unpack($docRootTo.$pack_to);

                if (!$uRes)
                {
                    self::log($arc->GetErrors());
                }
            }
            TaskFileTable::update($arTaskFile['ID'], [
                'STATUS' => TaskFileTable::STATUS_SUCCESS,
            ]);
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
        //file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/task_log.txt', date('d.m.Y H:i:s') . ': ' . $mess . "\n", FILE_APPEND);
    }

}