<?php
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php");
IncludeModuleLangFile(__FILE__);
global $APPLICATION;
$sModuleDir = dirname(dirname(__FILE__));
$sModuleId = basename($sModuleDir);

\Bitrix\Main\Loader::includeModule($sModuleId);
\Bitrix\Main\Loader::includeModule('iblock');

$APPLICATION->SetTitle(GetMessage("SMARTCAT_CONNECTOR_ZAKAZY"));


$arStatus = \Smartcat\Connector\TaskTable::getAccessibleStatusList();
$arStatusAll = \Smartcat\Connector\TaskTable::getStatusList();

$arProfiles = \Smartcat\Connector\ProfileTable::getList([
    'order' => ['NAME' => 'asc'],
    'filter' => ['=ACTIVE' => 'Y'],
])->fetchAll();

$sTableID = "tbl_smartcat_connector_tasks";
$oSort = new CAdminSorting($sTableID, "id", "desc");
$lAdmin = new CAdminList($sTableID, $oSort);

if ($lAdmin->EditAction()) {
    foreach ($_REQUEST['FIELDS'] as $ID => $arFields) {
        if (!$lAdmin->IsUpdated($ID)) continue;
        $arFields['ACTIVE'] = ($arFields['ACTIVE'] == 'Y');
        \Smartcat\Connector\TaskTable::update($ID, $arFields);
    }
}

if ($arID = $lAdmin->GroupAction()) {
    foreach ($arID as $ID) {
        if (strlen($ID) <= 0) continue;
        $ID = IntVal($ID);
        switch ($_REQUEST['action']) {
            case "delete":
                \Smartcat\Connector\TaskTable::delete($ID);
                break;
            case "status":
            case "refrash": 
                $arTask = \Smartcat\Connector\TaskTable::getById($ID)->fetch();

                if ($arTask && $arTask['STATUS'] === \Smartcat\Connector\TaskTable::STATUS_SUCCESS) {
                    \Smartcat\Connector\TaskTable::update($ID, [
                        'STATUS' => \Smartcat\Connector\TaskTable::STATUS_PROCESS,
                        'COMMENT' => '',
                    ]);

                    $rsFiles = \Smartcat\Connector\TaskFileTable::getList([
                        'filter' => [
                            '=TASK_ID' => $ID,
                        ]
                    ]);
                    while ($arFile = $rsFiles->fetch()) {
                        \Smartcat\Connector\TaskFileTable::update($arFile['ID'], [
                            'STATUS' => \Smartcat\Connector\TaskFileTable::STATUS_PROCESS,
                        ]);
                    }
                }
                break;
        }
    }
}

$arHeader = array(
    array(
        "id" => "ID",
        "content" => "ID",
        "default" => true,
        "sort" => "ID",
    ),
    array(
        "id" => "PROFILE",
        "content" => GetMessage("SMARTCAT_CONNECTOR_PROFILQ"),
        "default" => true,
        //"sort" => "PROFILE_ID",
    ),
    array(
        "id" => "ELEMENT",
        "content" => GetMessage("SMARTCAT_CONNECTOR_ELEMENT"),
        "default" => true,
    ),
    array(
        "id" => "LANGUAGES",
        "content" => GetMessage("SMARTCAT_CONNECTOR_PEREVOD"),
        "default" => true,
    ),
    array(
        "id" => "PROJECT_NAME",
        "content" => GetMessage("SMARTCAT_CONNECTOR_PROJECT"),
        "default" => true,
    ),
    array(
        "id" => "STATUS",
        "content" => GetMessage("SMARTCAT_CONNECTOR_STATUS"),
        "default" => true,
    ),
    array(
        "id" => "DEADLINE",
        "content" => GetMessage("SMARTCAT_CONNECTOR_DEDLAYN"),
        "default" => true,
    ),
    array(
        "id" => "COMMENT",
        "content" => GetMessage("SMARTCAT_CONNECTOR_PRIMECANIE"),
        "default" => true,
    ),
);


$lAdmin->AddHeaders($arHeader);

$nav = new \Bitrix\Main\UI\AdminPageNavigation("nav-taks");

$filter = [];

if ($_REQUEST['set_filter']) {

    if ($find_status) {
        $filter['=STATUS'] = $find_status;
    }

    if ($find_profile) {
        $filter['=PROFILE_ID'] = $find_profile;
    }

}

$arStat = [];

foreach (\Smartcat\Connector\TaskTable::getStatusList() as $sStatusID => $sStatusName) {
    $arStat[$sStatusName] = \Smartcat\Connector\TaskTable::getCount(array_merge($filter, ['=STATUS' => $sStatusID]));
}

$rsItems = \Smartcat\Connector\TaskFileTable::getList(array(
    'order' => array(strtoupper($by) => $order),
    'count_total' => true,
    'offset' => $nav->getOffset(),
    'limit' => $nav->getLimit(),
    'filter' => $filter,
));

$nav->setRecordCount($rsItems->getCount());

$lAdmin->setNavigation($nav, GetMessage("SMARTCAT_CONNECTOR_ZADANIA"));

$arTypes = \Smartcat\Connector\ProfileTable::getTypeList();

$apiServer = \Bitrix\Main\Config\Option::get('smartcat.connector', 'api_server');

$tasks = [];
$profiles = [];

while ($arTaskFile = $rsItems->fetch()) {

    if(!isset($tasks[$arTaskFile['TASK_ID']])){
        $tasks[$arTaskFile['TASK_ID']] = \Smartcat\Connector\TaskTable::getById($arTaskFile['TASK_ID'])->fetch();
    }
    $arTask = $tasks[$arTaskFile['TASK_ID']];

    if(!isset($profiles[$arTask['PROFILE_ID']])){
        $profiles[$arTask['PROFILE_ID']] = \Smartcat\Connector\ProfileTable::getById($arTask['PROFILE_ID'])->fetch();
    }
    $arProfile = $profiles[$arTask['PROFILE_ID']];

    $arRow = [];
    $arRow['ID'] = $arTask['ID'];
    $arRow['COMMENT'] = $arTask['COMMENT'];
    $arRow['DEADLINE'] = '&mdash;';
    $arRow['STATUS'] = $arStatusAll[$arTask['STATUS']];

    if( $arTask['DEADLINE'] && $arTask['DEADLINE']->getTimestamp() > 1 ){
        $arRow['DEADLINE'] = date('Y-m-d H:i', $arTask['DEADLINE']->getTimestamp() );
    }

    if(!empty($arTaskFile['DOCUMENT_ID'])){
        $docIds = explode('_',$arTaskFile['DOCUMENT_ID']);
        $projectLink = "<a href=\"//$apiServer/editor?DocumentId={$docIds[0]}&LanguageId={$docIds[1]}\" target=\"blank\" >";
        $arRow['PROJECT_NAME'] = $projectLink . $arTask['PROJECT_NAME'] . '</a>';
    }else{
        $arRow['PROJECT_NAME'] = '&mdash;';
    }

    $sProfileLink = '/bitrix/admin/smartcat.connector_profile.php?ID=' . $arProfile['ID'] . '&lang=ru';
    $arRow['PROFILE'] = $arProfile['NAME'] . ' [<a href="' . $sProfileLink . '" target="_blank">' . $arTask['PROFILE_ID'] . '</a>]';

    $arIBlock = CIBlock::GetByID($arProfile['IBLOCK_ID'])->Fetch();

    $arElement = CIBlockElement::GetByID($arTask['ELEMENT_ID'])->Fetch();
    $sElementLink = '/bitrix/admin/iblock_element_edit.php?IBLOCK_ID=' . $arProfile['IBLOCK_ID'] . '&type=' . $arIBlock['IBLOCK_TYPE_ID'] . '&ID=10834&lang=ru&find_section_section=0&WF=Y';

    $arRow['ELEMENT'] = $arElement['NAME'] . ' [<a href="' . $sElementLink . '" target="_blank">' . $arTask['ELEMENT_ID'] . '</a>]';



    $arLang = [];
    $sLangRow = '<a href="/bitrix/admin/smartcat.connector_content.php?lang=ru&TASK_ID=' . $arTask['ID'] . '" target="_blank">' . $arTaskFile['LANG_FROM'] . '</a> -> ';
    if ($arTaskFile['STATUS'] == \Smartcat\Connector\TaskFileTable::STATUS_SUCCESS) {
        $sLangRow .= '<a href="/bitrix/admin/smartcat.connector_content.php?lang=ru&FILE_ID=' . $arTaskFile['ID'] . '" target="_blank">' . $arTaskFile['LANG_TO'] . '</a>';
    } else {
        $sLangRow .= $arTaskFile['LANG_TO'];
    }
    $arLang[] = $sLangRow;
    $sVendor = explode('|',$arTask['VENDOR'])[1];

    $arRow['LANGUAGES'] = $sVendor . ':<br>' . implode("<br>", $arLang);

    $row = &$lAdmin->AddRow($arRow['ID'], $arRow);

    $row->AddViewField('ELEMENT', $arRow['ELEMENT']);
    $row->AddViewField('LANGUAGES', $arRow['LANGUAGES']);
    $row->AddViewField('PROFILE', $arRow['PROFILE']);
    $row->AddViewField('PROJECT_NAME', $arRow['PROJECT_NAME']);

    $arActions = [];
    if($arTask['STATUS'] === \Smartcat\Connector\TaskTable::STATUS_SUCCESS){
        $arActions[] = array(
            "ICON" => "edit",
            "TEXT" => GetMessage("SMARTCAT_CONNECTOR_REFRESH"),
            "ACTION" => $lAdmin->ActionDoGroup($arRow['ID'], "status"), // "if(confirm('".GetMessage("SMARTCAT_CONNECTOR_UDALITQ_PROFILQ") . 
        );
    }

    $arActions[] = array(
        "ICON" => "delete",
        "TEXT" => GetMessage("SMARTCAT_CONNECTOR_UDALITQ"),
        "ACTION" => "if(confirm('".GetMessage("SMARTCAT_CONNECTOR_UDALITQ_PROFILQ") . $lAdmin->ActionDoGroup($arRow['ID'], "delete")
    );

    $row->AddActions($arActions);
}


$arActions = array(
    "delete" => GetMessage("MAIN_ADMIN_LIST_DELETE"),
);
$arParams = array();

$lAdmin->AddGroupActionTable($arActions, $arParams);

$lAdmin->CheckListMode();


require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_after.php");
?>
<form method="GET" name="find_form" id="find_form" action="<? echo $APPLICATION->GetCurPage() ?>">
    <?
    $arFindFields = Array();
    $arFindFields["STATUS"] = GetMessage("SMARTCAT_CONNECTOR_STATUS");

    $filterUrl = $APPLICATION->GetCurPageParam();
    $oFilter = new CAdminFilter($sTableID . "_filter", $arFindFields, array("table_id" => $sTableID, "url" => $filterUrl));
    ?>
    <script type="text/javascript">
        var arClearHiddenFields = new Array();
        function applyFilter(el) {
            BX.adminPanel.showWait(el);
            <?=$sTableID . "_filter";?>.
            OnSet('<?=CUtil::JSEscape($sTableID)?>', '<?=CUtil::JSEscape($filterUrl)?>');
            return false;
        }

        function deleteFilter(el) {
            BX.adminPanel.showWait(el);
            if (0 < arClearHiddenFields.length) {
                for (var index = 0; index < arClearHiddenFields.length; index++) {
                    if (undefined != window[arClearHiddenFields[index]]) {
                        if ('ClearForm' in window[arClearHiddenFields[index]]) {
                            window[arClearHiddenFields[index]].ClearForm();
                        }
                    }
                }
            }
            <?=$sTableID . "_filter"?>.
            OnClear('<?=CUtil::JSEscape($sTableID)?>', '<?=CUtil::JSEscape($APPLICATION->GetCurPage() . '?lang=' . urlencode(LANG) . '&')?>');
            return false;
        }
    </script>
    <?
    $oFilter->Begin();
    ?>
    <tr>
        <td><?=GetMessage("SMARTCAT_CONNECTOR_PROFILQ")?></td>
        <td>
            <select name="find_profile" id="find_profile">
                <option value=""><?=GetMessage("SMARTCAT_CONNECTOR_VSE")?></option>
                <? foreach ($arProfiles as $arProfile): ?>
                    <option
                            value="<?= $arProfile['ID']; ?>" <?= ($arProfile['ID'] == $find_profile ? 'selected' : ''); ?>>
                        <?= $arProfile['NAME']; ?>
                    </option>
                <? endforeach; ?>
            </select>

        </td>
    </tr>
    <tr>
        <td><?=GetMessage("SMARTCAT_CONNECTOR_STATUS")?></td>
        <td>
            <select name="find_status" id="find_status">
                <option value=""><?=GetMessage("SMARTCAT_CONNECTOR_VSE")?></option>
                <? foreach (\Smartcat\Connector\TaskTable::getStatusList() as $sCode => $sStatus): ?>
                    <option value="<?= $sCode; ?>" <?= ($sCode == $find_status ? 'selected' : ''); ?>>
                        <?= $sStatus; ?>
                    </option>
                <? endforeach; ?>
            </select>

        </td>
    </tr>
    <?
    $oFilter->Buttons();
    ?><input class="adm-btn" type="submit" name="set_filter"
             value="<?=GetMessage("SMARTCAT_CONNECTOR_NAYTI")?>"
             title="<?=GetMessage("SMARTCAT_CONNECTOR_NAYTI")?>" onClick="return applyFilter(this);">
    <input class="adm-btn" type="submit" name="del_filter"
           value="<?=GetMessage("SMARTCAT_CONNECTOR_OTMENITQ")?>"
           title="<?=GetMessage("SMARTCAT_CONNECTOR_OTMENITQ")?>"
           onClick="deleteFilter(this); return false;">
    <?
    $oFilter->End();
    ?>
</form>

<? $lAdmin->DisplayList(); ?>

<? echo BeginNote(); ?>
<? foreach ($arStat as $sStatus => $iCount): ?>
    <?= $sStatus; ?>: <b><?= $iCount; ?></b><br>
<? endforeach; ?>
<? echo EndNote(); ?>



<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_admin.php"); ?>
