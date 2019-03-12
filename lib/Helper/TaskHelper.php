<?php

namespace Smartcat\Connector\Helper;


use Bitrix\Main\Loader;
use Smartcat\Connector\ProfileIblockTable;
use Smartcat\Connector\ProfileTable;
use Smartcat\Connector\TaskFileTable;
use Smartcat\Connector\TaskTable;
use Bitrix\Main\Type\DateTime;

class TaskHelper
{

    public static function setProject($task_ids, $project)
    {
        $rsTasks = TaskTable::getList([
            'order' => ['ID' => 'asc'],
            'filter' => [
                '=ID' => $task_ids,
            ]
        ]);
        while ($arTask = $rsTasks->fetch()) {
            if(is_object($project)){
                $data_for_update = [
                    'PROJECT_ID' => $project->getId(),
                    'PROJECT_NAME' => $project->getName(),
                ];
            }else{
                //work if project not created on smartcat server
                $data_for_update = $project;
            }
            TaskTable::update($arTask['ID'],$data_for_update);
        }
    }
    public static function createForElement($ID, $IBLOCK_ID = null, $profileID = null, $deadline = null)
    {

        if (empty($IBLOCK_ID)) {
            if (!Loader::includeModule('iblock')) return false;
            $arElement = \CIBlockElement::GetByID($ID)->Fetch();
            if ($arElement) {
                $IBLOCK_ID = $arElement['IBLOCK_ID'];
            } else {
                return false;
            }
        }

        $arProfileFilter = [
            '=IBLOCK_ID' => intval($IBLOCK_ID),
        ];

        $targetElementId = 0;
        if ($profileID > 0) {
            $arProfileFilter['=ID'] = $profileID;

            $anySuccesfulTask = TaskTable::getList([
                'order' => ['ID' => 'asc'],
                'filter' => [
                    '=STATUS' => TaskTable::STATUS_SUCCESS,
                    '=PROFILE_ID' => $profileID,
                    '=ELEMENT_ID' => $ID,
                ]
            ])->fetch();
            if(!empty($anySuccesfulTask)){
                $anySuccesfulTaskFile =  TaskFileTable::getList([
                    'order' => ['ID' => 'asc'],
                    'filter' => [
                        '=TASK_ID' => $anySuccesfulTask['ID'],
                    ]
                ])->fetch();
                if(!empty($anySuccesfulTaskFile)){
                    $targetElementId = $anySuccesfulTaskFile['ELEMENT_ID'];
                }
            }

        }

        $rsProfiles = ProfileTable::getList([
            'filter' => $arProfileFilter,
        ]);

        while ($arProfile = $rsProfiles->fetch()) {
            $arTask = [
                'PROFILE_ID' => $arProfile['ID'],
                'ELEMENT_ID' => $ID,
                'VENDOR' => $arProfile['VENDOR'],
                'STATUS' => TaskTable::STATUS_READY_UPLOAD,
                'CONTENT' => self::prepareElementContent($ID, $arProfile['FIELDS']),
            ];

            if($deadline){
                $arTask['DEADLINE'] = DateTime::createFromTimestamp($deadline);
            }

            $taskID = 0;

            $result = TaskTable::add($arTask);
            if ($result->isSuccess()) {
                $taskID = $result->getId();
            }else{
                echo '<pre>' . print_r($result->getErrorMessages(), true) . '</pre>';
            }

            if ($taskID > 0) {
                $rsIBlocks = ProfileIblockTable::getList([
                    'filter' => [
                        '=PROFILE_ID' => $arProfile['ID'],
                    ],
                ]);

                $arIBlocks = $rsIBlocks->fetchAll();

                foreach ($arIBlocks as $arIBlock) {

                    $arTaskFile = [
                        'TASK_ID' => $taskID,
                        'LANG_FROM' => $arProfile['LANG'],
                        'LANG_TO' => $arIBlock['LANG'],
                    ];

                    if($targetElementId > 0){
                        $arTaskFile['ELEMENT_ID'] = $targetElementId;
                    }

                    $res = TaskFileTable::add($arTaskFile);

                    if (!$res->isSuccess()) {
                        echo '<pre>' . print_r($res->getErrorMessages(), true) . '</pre>';
                        die();
                    }
                }
            }

        }
        return $taskID;
    }

    public static function prepareElementContent($elementID, $arFields = [])
    {
        Loader::includeModule('iblock');

        $sContent = '';

        $obElement = \CIBlockElement::GetByID($elementID)->GetNextElement(true, false);

        if ($obElement) {

            $arElement = $obElement->GetFields();
            $arProps = $obElement->GetProperties();

            foreach ($arFields['FIELDS'] as $sFieldCode) {

                if ($sFieldCode == 'IBLOCK_SECTION_ID') {
                    if ($arElement['IBLOCK_SECTION_ID'] > 0) {
                        $rsSections = \CIBlockSection::GetNavChain($arElement['IBLOCK_ID'], $arElement['IBLOCK_SECTION_ID'], ['ID', 'NAME']);

                        $i = 0;
                        while ($arSection = $rsSections->Fetch()) {
                            $sContent .= '<field id="' . $sFieldCode . '_' . $i . '">' . $arSection['NAME'] . '</field>' . PHP_EOL;
                            $i++;
                        }

                    }
                    continue;
                }


                $sContent .= '<field id="' . $sFieldCode . '">' . $arElement[$sFieldCode] . '</field>' . PHP_EOL;
            }

            foreach ($arFields['PROPS'] as $sPropCode) {
                $arProp = $arProps[$sPropCode];
                $sPropValue = $arProp['MULTIPLE'] == 'Y' ? implode('##', $arProp['VALUE']) : $arProp['VALUE'];
                $sContent .= '<field id="PROP_' . $sPropCode . '">' . $sPropValue . '</field>' . PHP_EOL;
            }
        }

        $sContent = str_replace('&nbsp;', '', $sContent); // smartcat.connector bug
        return $sContent;
    }
}