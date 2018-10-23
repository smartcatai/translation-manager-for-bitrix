<?php

namespace Abbyy\Cloud\Events;

use Abbyy\Cloud\Helper\TaskHelper;
use Abbyy\Cloud\ProfileIblockTable;
use Abbyy\Cloud\ProfileTable;
use Abbyy\Cloud\TaskTable;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class Iblock
{

    public static function OnAfterIBlockElementAdd(&$arFields)
    {
        if ($arFields['ID'] > 0) {

            $arProfiles = ProfileTable::getList([
                'filter' => [
                    '=IBLOCK_ID' => intval($arFields['IBLOCK_ID']),
                    '=AUTO_ORDER' => 'Y',
                ],
            ])->fetchAll();
            foreach ($arProfiles as $arProfile) {
                TaskHelper::createForElement($arFields['ID'], $arProfile['IBLOCK_ID'], $arProfile['ID']);
            }
        }
    }

    public static function OnAfterIBlockElementUpdate(&$arFields)
    {

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

        if ($bListPage && Loader::includeModule('iblock') && Loader::includeModule('abbyy.cloud')) {
            $iblockID = intval($_REQUEST['IBLOCK_ID']);

            $arProfiles = ProfileTable::getList([
                'filter' => [
                    '=IBLOCK_ID' => $iblockID,
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

                        $list->arActions['abbyy_cloud_translate_' . $arProfile['ID']] = GetMessage("ABBYY_CLOUD_PEREVOD") . $arTypes[$arProfile['TYPE']] . ' (' . implode(', ', $arProfile['LANGS']) . ')';

                        $sMessage = Loc::getMessage('ABBYY_CLOUD_PROFILE_TASK_EXIST', [
                            '#STATUS#' => TaskTable::getStatusList()[$arTask['STATUS']]
                        ]);

                        global $APPLICATION;
                        $link = \CUtil::AddSlashes($APPLICATION->GetCurPage()) . "?ID=" . \CUtil::AddSlashes($row->id) . "&action_button=abbyy_cloud_translate&lang=" . LANGUAGE_ID . "&" . bitrix_sessid_get() . "&" . \CUtil::AddSlashes('&type=' . urlencode($_REQUEST['type']) . '&lang=' . LANGUAGE_ID . '&IBLOCK_ID=' . $IBLOCK_ID . '&PROFILE_ID=' . $arProfile['ID'] . ($find_section ? '&find_section_section=' . $find_section : '') . '&find_el_y=' . $find_el);

                        if ($arTask) {
                            $row->aActions[] = [
                                //'ICON' => 'copy',
                                'TEXT' => GetMessage("ABBYY_CLOUD_PEREVOD") . $arTypes[$arProfile['TYPE']] . ' (' . implode(', ', $arProfile['LANGS']) . ')',
                                'ONCLICK' => 'if(confirm("' . $sMessage . '")) ShowDeadlineDialog("' . $lAdmin->table_id . '", "' . $link . '")'
                            ];
                        } else {
                            $row->aActions[] = [

                                //'ICON' => 'copy',
                                'TEXT' => GetMessage("ABBYY_CLOUD_PEREVOD") . $arTypes[$arProfile['TYPE']] . ' (' . implode(', ', $arProfile['LANGS']) . ')',
                                'ACTION' => 'ShowDeadlineDialog("' . $lAdmin->table_id . '", "' . $link . '")',
                            ];
                        }
                    }
                }

            }
        }
        if ($_REQUEST['action_button'] == 'abbyy_cloud_translate') {
            $GLOBALS['APPLICATION']->RestartBuffer();
            die();
        }
    }

    public static function OnBeforePrologHandler()
    {
        \CUtil::InitJSCore(['jquery']);
        \Bitrix\Main\Page\Asset::getInstance()->addJs('/bitrix/js/abbyy.cloud/abbyy.cloud.js');

        $strCurPage = $GLOBALS['APPLICATION']->GetCurPage();
        $bListPage = ($strCurPage == '/bitrix/admin/iblock_element_admin.php' ||
            $strCurPage == '/bitrix/admin/iblock_list_admin.php'
        );

        if (substr($_REQUEST['action'], 0, 22) == 'abbyy_cloud_translate_') {
            $_REQUEST['PROFILE_ID'] = intval(str_replace('abbyy_cloud_translate_', '', $_REQUEST['action']));
            $_REQUEST['action'] = 'abbyy_cloud_translate';
        }


        if (check_bitrix_sessid() && $bListPage && $_REQUEST['PROFILE_ID'] > 0 && Loader::includeModule('iblock') && Loader::includeModule('abbyy.cloud')) {
            if (!is_array($_REQUEST['ID'])) $_REQUEST['ID'] = [$_REQUEST['ID']];

            if ($_REQUEST['action'] == 'abbyy_cloud_translate' || $_REQUEST['action_button'] == 'abbyy_cloud_translate') {
                foreach ($_REQUEST['ID'] as $ID) {
                    if ($ID[0] == 'S') continue;
                    if ($ID[0] == 'E') $ID = substr($ID, 1);
                    TaskHelper::createForElement($ID, intval($_REQUEST['IBLOCK_ID']), intval($_REQUEST['PROFILE_ID']), $_REQUEST['deadline']);
                }
            }

        }
    }

}