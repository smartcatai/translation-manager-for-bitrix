<?php

namespace Smartcat\Connector\Events;

use Smartcat\Connector\Helper\LoggerHelper;
use Smartcat\Connector\Helper\TaskHelper;
use Smartcat\Connector\Tables\ProfileTable;
use Smartcat\Connector\Repository\ProfileRepository;
use Smartcat\Connector\ProfileIblockTable;
use Smartcat\Connector\TaskTable;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class Iblock
{
    protected static $iBlockUpdated = false;
    protected static $iBlockAdd = false;
    protected static $createTask = false;
    const ACTION_NAME = 'smartcat_connector_translate';

    /**
     * @param $arFields
     */
    public static function onBeforeIBlockElementAdd(&$arFields)
    {
        self::$iBlockAdd = true;

        $obElement = \CIBlockElement::GetByID($arFields['ID'])->GetNextElement(true, false);

        if (!$obElement) {
            self::$createTask = true;
            return;
        }

        self::enumProfiles($obElement, $arFields);
    }

    /**
     * @param $arFields
     */
    public static function onBeforeIBlockElementUpdate(&$arFields)
    {
        self::$iBlockUpdated = true;

        $obElement = \CIBlockElement::GetByID($arFields['ID'])->GetNextElement(true, false);
        self::enumProfiles($obElement, $arFields);
    }

    /**
     * @param $obElement \_CIBElement
     * @param $arFields array
     */
    protected static function enumProfiles($obElement, $arFields)
    {
        $arElement = $obElement->GetFields();
        $arProfiles = ProfileRepository::getAllByIblockId($arFields['IBLOCK_ID']);

        foreach ($arProfiles as $arProfile) {
            foreach ($arProfile['FIELDS'] as $fieldList) {
                foreach ($fieldList as $field) {
                    if (!isset($arFields[$field])) {
                        continue;
                    }

                    if ($arFields[$field] === $arElement[$field]) {
                        continue;
                    }

                    self::$createTask = true;
                    break;
                }

                if (self::$createTask) {
                    break;
                }
            }
        }
    }

    public static function OnAfterIBlockElementAdd(&$arFields)
    {
        if ($arFields['ID'] > 0) {
            $arProfiles = ProfileRepository::getAllByIblockId($arFields['IBLOCK_ID']);

            foreach ($arProfiles as $arProfile) {
                if (self::$iBlockAdd && self::$createTask) {
                    $task_id = TaskHelper::createForElement($arFields['ID'], $arProfile['IBLOCK_ID'], $arProfile['ID']);
                    try{
                        $project = \Smartcat\Connector\Helper\ApiHelper::createProject($arProfile, $arFields['NAME']);
                    }catch(\Http\Client\Common\Exception\ClientErrorException $e){
                        LoggerHelper::error('events.lblock', 'API Error: ' . $e->getResponse()->getBody()->getContents());
                        var_dump($e->getResponse()->getBody()->getContents()); die;
                    }
                    TaskHelper::setProject([$task_id], $project);
                }
            }
        }
    }

    public static function OnAfterIBlockElementUpdate(&$arFields)
    {
        if ($arFields['ID'] > 0) {
            $arProfiles = ProfileRepository::getAllByIblockId($arFields['IBLOCK_ID']);

            foreach ($arProfiles as $arProfile) {
                if(self::$iBlockUpdated && self::$createTask){
                    $task_id = TaskHelper::createForElement($arFields['ID'], $arProfile['IBLOCK_ID'], $arProfile['ID']);
                    try {
                        if ($arProfile['PROJECT_ID']) {
                            $project = \Smartcat\Connector\Helper\ApiHelper::getProject($arProfile['PROJECT_ID']);
                        }

                        if (!$project) {
                            $project = \Smartcat\Connector\Helper\ApiHelper::createProject($arProfile, $arFields['NAME']);
                        }
                    } catch (\Http\Client\Common\Exception\ClientErrorException $e) {
                        LoggerHelper::error('events.lblock', 'API Error: ' . $e->getResponse()->getBody()->getContents());
                        var_dump($e->getResponse()->getBody()->getContents()); die;
                    }
                    TaskHelper::setProject([$task_id], $project);
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
            $arProfiles = ProfileRepository::getAllByIblockId(intval($_REQUEST['IBLOCK_ID']));

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

                        $list->arActions[self::ACTION_NAME . '_' . $arProfile['ID']] = GetMessage("SMARTCAT_CONNECTOR_PEREVOD") . $arTypes[$arProfile['TYPE']] . ' (' . implode(', ', $arProfile['LANGS']) . ')';

                        $sMessage = Loc::getMessage('SMARTCAT_CONNECTOR_PROFILE_TASK_EXIST', [
                            '#STATUS#' => TaskTable::getStatusList()[$arTask['STATUS']]
                        ]);

                        global $APPLICATION;
                        $link = \CUtil::AddSlashes($APPLICATION->GetCurPage()) 
                                . "?ID=" . \CUtil::AddSlashes($row->id) 
                                . "&action_button=" . self::ACTION_NAME
                                . "&lang=" . LANGUAGE_ID 
                                . "&" . bitrix_sessid_get() 
                                . "&" . \CUtil::AddSlashes('&type=' . urlencode($_REQUEST['type']) 
                                    . '&lang=' . LANGUAGE_ID 
                                    . '&IBLOCK_ID=' . $IBLOCK_ID 
                                    . '&PROFILE_ID=' . $arProfile['ID']
                                    . ($find_section ? '&find_section_section=' . $find_section : '')
                                    . '&find_el_y=' . $find_el
                                );

                        if ($arTask) {
                            $row->aActions[] = [
                                'TEXT' => GetMessage("SMARTCAT_CONNECTOR_PEREVOD") . $arTypes[$arProfile['TYPE']] . ' (' . implode(', ', $arProfile['LANGS']) . ')',
                                'ONCLICK' => 'if(confirm("' . $sMessage . '")) ShowDeadlineDialog("' . $lAdmin->table_id . '", "' . $link . '")',
                                'ACTION' => 'if(confirm("' . $sMessage . '")) ShowDeadlineDialog("' . $lAdmin->table_id . '", "' . $link . '")'
                            ];
                        } else {
                            $row->aActions[] = [
                                'TEXT' => GetMessage("SMARTCAT_CONNECTOR_PEREVOD") . $arTypes[$arProfile['TYPE']] . ' (' . implode(', ', $arProfile['LANGS']) . ')',
                                'ONCLICK' => 'ShowDeadlineDialog("' . $lAdmin->table_id . '", "' . $link . '")',
                                'ACTION' => 'ShowDeadlineDialog("' . $lAdmin->table_id . '", "' . $link . '")',
                            ];
                        }
                    }
                }

            }
        }
        if ($_REQUEST['action_button'] == self::ACTION_NAME) {
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
    
        $action = '';
        $requestAction = '';
        $profileId = isset($_REQUEST['PROFILE_ID']) ? $_REQUEST['PROFILE_ID'] : '';

        if(is_array($_REQUEST['action'])){
            foreach($_REQUEST['action'] as $actionName){
                if (strpos($actionName, self::ACTION_NAME) !== false) {
                    $requestAction = $actionName;
                    break;
                }
            }
        }elseif(strpos($_REQUEST['action'], self::ACTION_NAME) !== false){
            $requestAction = $_REQUEST['action'];
        }

        if (!empty($requestAction) ) {
            $profileId = intval(str_replace(self::ACTION_NAME . '_', '', $requestAction));
        }

        $action = self::ACTION_NAME;

        if (check_bitrix_sessid() && $bListPage && $profileId > 0 && Loader::includeModule('iblock') && Loader::includeModule('smartcat.connector')) {
            if (!is_array($_REQUEST['ID'])) $_REQUEST['ID'] = [$_REQUEST['ID']];

            if ($action == self::ACTION_NAME || $_REQUEST['action_button'] == self::ACTION_NAME) {
                $arProfile = ProfileRepository::getOneById($profileId);
                $task_ids = [];
                $project_names = [];

                foreach ($_REQUEST['ID'] as $ID) {
                    if ($ID[0] == 'S') {
                        $ID = substr($ID, 1);
                        $res = \CIBlockElement::GetList(["SORT"=>"ASC",], ['IBLOCK_ID'=>$_REQUEST['IBLOCK_ID'],'IBLOCK_SECTION_ID' => $ID]);
                        while($element = $res->GetNext(true, false)){
                            $project_names[]= $element['NAME'];
                            $task_ids[] = TaskHelper::createForElement($element['ID'], intval($_REQUEST['IBLOCK_ID']), intval($profileId));
                        }
                    } else {
                        if ($ID[0] == 'E') {
                            $ID = substr($ID, 1);
                        }
                        $arElement = \CIBlockElement::GetByID($ID)->GetNextElement(true, false)->GetFields();
                        $project_names[]= $arElement['NAME'];
                        $task_ids[] = TaskHelper::createForElement($ID, intval($_REQUEST['IBLOCK_ID']), intval($profileId));
                    }
                }

                if( !empty($project_names) && !empty($task_ids) ){
                    $project = \Smartcat\Connector\Helper\ApiHelper::createProject($arProfile, implode(', ',$project_names));
                    TaskHelper::setProject($task_ids, $project);
                }
            }
        }

    }

}