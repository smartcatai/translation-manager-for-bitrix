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

        if ($profileID > 0) {
            $arProfileFilter['=ID'] = $profileID;
        }

        $rsProfiles = ProfileTable::getList([
            'filter' => $arProfileFilter,
        ]);

        $datatime = (new \DateTime('now'))->modify(' + 1 day');

        while ($arProfile = $rsProfiles->fetch()) {
            $arTask = [
                'PROFILE_ID' => $arProfile['ID'],
                'ELEMENT_ID' => $ID,
                'VENDOR' => $arProfile['VENDOR'],
                'DEADLINE' =>  $datatime ? DateTime::createFromTimestamp($datatime->getTimestamp()) : '',
                'STATUS' => TaskTable::STATUS_READY_UPLOAD,
                'CONTENT' => self::prepareElementContent($ID, $arProfile['FIELDS']),
            ];

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

                    $res = TaskFileTable::add($arTaskFile);

                    if (!$res->isSuccess()) {
                        echo '<pre>' . print_r($res->getErrorMessages(), true) . '</pre>';
                        die();
                    }
                }
            }

        }
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