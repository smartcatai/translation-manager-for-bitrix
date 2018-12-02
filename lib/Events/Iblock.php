<?php

namespace Smartcat\Connector\Events;

use Smartcat\Connector\Helper\TaskHelper;
use Smartcat\Connector\ProfileIblockTable;
use Smartcat\Connector\ProfileTable;
use Smartcat\Connector\TaskTable;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class Iblock
{
    protected static $iBlockUpdated = false;
    protected static $iBlockAdd = false;
    protected static $createTask = false;

    public static function onBeforeIBlockElementAdd(&$arFields)
    {
        self::$iBlockAdd = true;
        $obElement = \CIBlockElement::GetByID($arFields['ID'])->GetNextElement(true, false);
        if(!$obElement){
            self::$createTask = true;
            return;
        }
        $arElement = $obElement->GetFields();
        $arProfiles = ProfileTable::getList([
            'filter' => [
                '=IBLOCK_ID' => intval($arFields['IBLOCK_ID']),
                '=ACTIVE' => 'Y',
                '=AUTO_ORDER' => 'Y',
            ],
        ])->fetchAll();
        foreach ($arProfiles as $arProfile) {
            foreach($arProfile['FIELDS'] as $fieldList){
                foreach($fieldList as $field){
                    if(!isset($arFields[$field])){
                        continue;
                    }
                    if($arFields[$field] === $arElement[$field]){
                        continue;
                    }
                    self::$createTask = true;
                    break;
                }
                if(self::$createTask){
                    break;
                }
            }
        }
    }

    public static function onBeforeIBlockElementUpdate(&$arFields)
    {
        self::$iBlockUpdated = true;
        $obElement = \CIBlockElement::GetByID($arFields['ID'])->GetNextElement(true, false);
        $arElement = $obElement->GetFields();
        $arProfiles = ProfileTable::getList([
            'filter' => [
                '=IBLOCK_ID' => intval($arFields['IBLOCK_ID']),
                '=ACTIVE' => 'Y',
                '=AUTO_ORDER' => 'Y',
            ],
        ])->fetchAll();
        foreach ($arProfiles as $arProfile) {
            foreach($arProfile['FIELDS'] as $fieldList){
                foreach($fieldList as $field){
                    if(!isset($arFields[$field])){
                        continue;
                    }
                    if($arFields[$field] === $arElement[$field]){
                        continue;
                    }
                    self::$createTask = true;
                    break;
                }
                if(self::$createTask){
                    break;
                }
            }
        }
    }

    public static function OnAfterIBlockElementAdd(&$arFields)
    {
        if ($arFields['ID'] > 0) {
            $arProfiles = ProfileTable::getList([
                'filter' => [
                    '=IBLOCK_ID' => intval($arFields['IBLOCK_ID']),
                    '=ACTIVE' => 'Y',
                    '=AUTO_ORDER' => 'Y',
                ],
            ])->fetchAll();
            foreach ($arProfiles as $arProfile) {
                if(self::$iBlockAdd && self::$createTask){
                    // TaskHelper::createForElement($arFields['ID'], $arProfile['IBLOCK_ID'], $arProfile['ID']);
                }
            }
        }
    }

    public static function OnAfterIBlockElementUpdate(&$arFields)
    {
        if ($arFields['ID'] > 0) {
            $arProfiles = ProfileTable::getList([
                'filter' => [
                    '=IBLOCK_ID' => intval($arFields['IBLOCK_ID']),
                    '=ACTIVE' => 'Y',
                    '=AUTO_ORDER' => 'Y',
                ],
            ])->fetchAll();
            foreach ($arProfiles as $arProfile) {
                if(self::$iBlockUpdated && self::$createTask){
                    // TaskHelper::createForElement($arFields['ID'], $arProfile['IBLOCK_ID'], $arProfile['ID']);
                }
            }
        }
    }

    public static function OnAfterIBlockElementDelete($arFields)
    {
        $rsTasks = TaskTable::getList([
            'filter' => [
                '=ELEMENT_ID' => intval($arFields['ID']),
            ],
        ]);

        while ($arTask = $rsTasks->fetch()) {
            TaskTable::delete($arTask['ID']);
        }

    }


    /**
     * @param \CAdminList $list
     */
    public static function OnAdminListDisplayHandler(&$list)
    {
        $strCurPage = $GLOBALS['APPLICATION']->GetCurPage();
        $bListPage = ($strCurPage == '/bitrix/admin/iblock_element_admin.php' ||
            $strCurPage == '/bitrix/admin/iblock_list_admin.php'
        );

        $lAdmin = new \CAdminList($list->table_id, $list->sort);
        $IBLOCK_ID = intval($_REQUEST['IBLOCK_ID']);
        $find_section = intval($_REQUEST['find_section_section']);
        if ($find_section < 0)
            $find_section = 0;

        $find_el = $_REQUEST['find_el_y'] == 'Y' ? 'Y' : 'N';

        if ($find_el == 'Y') $find_section = '';

        if ($bListPage && Loader::includeModule('iblock') && Loader::includeModule('smartcat.connector')) {
            $iblockID = intval($_REQUEST['IBLOCK_ID']);

            $arProfiles = ProfileTable::getList([
                'filter' => [
                    '=IBLOCK_ID' => $iblockID,
                    '=ACTIVE' => 'Y',
                ],
            ])->fetchAll();

            foreach ($arProfiles as &$arProfile) {
                $arProfile['LANGS'] = [];
                $arIblocks = ProfileIblockTable::getList([
                    'filter' => [
                        '=PROFILE_ID' => $arProfile['ID'],
                    ],
                ])->fetchAll();
                foreach ($arIblocks as $arIblock) {
                    $arProfile['LANGS'][] = $arIblock['LANG'];
                }
            }
            unset($arProfile);

            $arTypes = ProfileTable::getTypeList();

            $arIDS = [];
            $arProfileElement = [];
            foreach ($list->aRows as $id => $row) {
                $arIDS[] = $row->id;
            }

            if (count($arIDS)) {
                $rsTasks = TaskTable::getList([
                    'filter' => [
                        'ELEMENT_ID' => $arIDS
                    ]
                ]);

                while ($arTask = $rsTasks->fetch()) {
                    $arProfileElement[$arTask['PROFILE_ID']][] = $arTask['ELEMENT_ID'];
                }
            }
            ob_start();
            ?>
            <script>
                var adminListTranslate = <?=\CUtil::PhpToJSObject($arProfileElement);?>;
            </script>
            <?
            $sString = ob_get_clean();

            \Bitrix\Main\Page\Asset::getInstance()->addString($sString);

            if ($arProfiles) {

                foreach ($list->aRows as $id => $row) {
                    foreach ($arProfiles as $arProfile) {

                        $list->arActions['smartcat_connector_translate_' . $arProfile['ID']] = GetMessage("SMARTCAT_CONNECTOR_PEREVOD") . $arTypes[$arProfile['TYPE']] . ' (' . implode(', ', $arProfile['LANGS']) . ')';

                        $sMessage = Loc::getMessage('SMARTCAT_CONNECTOR_PROFILE_TASK_EXIST', [
                            '#STATUS#' => TaskTable::getStatusList()[$arTask['STATUS']]
                        ]);

                        global $APPLICATION;
                        $link = \CUtil::AddSlashes($APPLICATION->GetCurPage()) . "?ID=" . \CUtil::AddSlashes($row->id) . "&action_button=smartcat_connector_translate&lang=" . LANGUAGE_ID . "&" . bitrix_sessid_get() . "&" . \CUtil::AddSlashes('&type=' . urlencode($_REQUEST['type']) . '&lang=' . LANGUAGE_ID . '&IBLOCK_ID=' . $IBLOCK_ID . '&PROFILE_ID=' . $arProfile['ID'] . ($find_section ? '&find_section_section=' . $find_section : '') . '&find_el_y=' . $find_el);

                        if ($arTask) {
                            $row->aActions[] = [
                                //'ICON' => 'copy',
                                'TEXT' => GetMessage("SMARTCAT_CONNECTOR_PEREVOD") . $arTypes[$arProfile['TYPE']] . ' (' . implode(', ', $arProfile['LANGS']) . ')',
                                'ONCLICK' => 'if(confirm("' . $sMessage . '")) ShowDeadlineDialog("' . $lAdmin->table_id . '", "' . $link . '")'
                            ];
                        } else {
                            $row->aActions[] = [

                                //'ICON' => 'copy',
                                'TEXT' => GetMessage("SMARTCAT_CONNECTOR_PEREVOD") . $arTypes[$arProfile['TYPE']] . ' (' . implode(', ', $arProfile['LANGS']) . ')',
                                'ONCLICK' => 'ShowDeadlineDialog("' . $lAdmin->table_id . '", "' . $link . '")',
                            ];
                        }
                    }
                }

            }
        }
        if ($_REQUEST['action_button'] == 'smartcat_connector_translate') {
            $GLOBALS['APPLICATION']->RestartBuffer();
            die();
        }
    }

    public static function OnBeforePrologHandler()
    {
        \CUtil::InitJSCore(['jquery']);
        \Bitrix\Main\Page\Asset::getInstance()->addJs('/bitrix/js/smartcat.connector/smartcat.connector.js');

        $strCurPage = $GLOBALS['APPLICATION']->GetCurPage();
        $bListPage = ($strCurPage == '/bitrix/admin/iblock_element_admin.php' ||
            $strCurPage == '/bitrix/admin/iblock_list_admin.php'
        );

        if (substr($_REQUEST['action'], 0, 22) == 'smartcat_connector_translate_') {
            $_REQUEST['PROFILE_ID'] = intval(str_replace('smartcat_connector_translate_', '', $_REQUEST['action']));
            $_REQUEST['action'] = 'smartcat_connector_translate';
        }


        if (check_bitrix_sessid() && $bListPage && $_REQUEST['PROFILE_ID'] > 0 && Loader::includeModule('iblock') && Loader::includeModule('smartcat.connector')) {
            if (!is_array($_REQUEST['ID'])) $_REQUEST['ID'] = [$_REQUEST['ID']];

            if ($_REQUEST['action'] == 'smartcat_connector_translate' || $_REQUEST['action_button'] == 'smartcat_connector_translate') {
                foreach ($_REQUEST['ID'] as $ID) {
                    if ($ID[0] == 'S') continue;
                    if ($ID[0] == 'E') $ID = substr($ID, 1);
                    TaskHelper::createForElement($ID, intval($_REQUEST['IBLOCK_ID']), intval($_REQUEST['PROFILE_ID']), $_REQUEST['deadline']);
                }
            }

        }
    }

}