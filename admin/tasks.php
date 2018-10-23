<?php
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php");
IncludeModuleLangFile(__FILE__);
global $APPLICATION;
$sModuleDir = dirname(dirname(__FILE__));
$sModuleId = basename($sModuleDir);

\Bitrix\Main\Loader::includeModule($sModuleId);
\Bitrix\Main\Loader::includeModule('iblock');

$APPLICATION->SetTitle(GetMessage("ABBYY_CLOUD_ZAKAZY"));


$arStatus = \Abbyy\Cloud\TaskTable::getStatusList();

$arProfiles = \Abbyy\Cloud\ProfileTable::getList([
    'order' => ['NAME' => 'asc'],
    'filter' => ['=ACTIVE' => 'Y'],
])->fetchAll();

$sTableID = "tbl_abbyy_cloud_tasks";
$oSort = new CAdminSorting($sTableID, "id", "desc");
$lAdmin = new CAdminList($sTableID, $oSort);

if ($lAdmin->EditAction()) {
    foreach ($_REQUEST['FIELDS'] as $ID => $arFields) {
        if (!$lAdmin->IsUpdated($ID)) continue;
        $arFields['ACTIVE'] = ($arFields['ACTIVE'] == 'Y');
        \Abbyy\Cloud\TaskTable::update($ID, $arFields);
    }
}

if ($arID = $lAdmin->GroupAction()) {
    foreach ($arID as $ID) {
        if (strlen($ID) <= 0) continue;
        $ID = IntVal($ID);
        switch ($_REQUEST['action']) {
            case "delete":
                \Abbyy\Cloud\TaskTable::delete($ID);
                break;
            case "status":
                if (array_key_exists($_REQUEST['status_to_move'], $arStatus)) {

                    $arTask = \Abbyy\Cloud\TaskTable::getById($ID)->fetch();
                    if ($arTask) {
                        \Abbyy\Cloud\TaskTable::update($ID, [
                            'STATUS' => $_REQUEST['status_to_move'],
                        ]);

                        $arStatusReset = [
                            \Abbyy\Cloud\TaskTable::STATUS_FAILED,
                            \Abbyy\Cloud\TaskTable::STATUS_CANCELED,
                        ];

                        if ($_REQUEST['status_to_move'] == \Abbyy\Cloud\TaskTable::STATUS_NEW) {
                            $rsFiles = \Abbyy\Cloud\TaskFileTable::getList([
                                'filter' => [
                                    '=TASK_ID' => $ID,
                                ]
                            ]);
                            while ($arFile = $rsFiles->fetch()) {
                                \Abbyy\Cloud\TaskFileTable::update($arFile['ID'], [
                                    'STATUS' => \Abbyy\Cloud\TaskFileTable::STATUS_NEW,
                                ]);
                            }
                        } elseif (in_array($arTask['STATUS'], $arStatusReset)
                            && !in_array($_REQUEST['status_to_move'], $arStatusReset)
                        ) {
                            $rsFiles = \Abbyy\Cloud\TaskFileTable::getList([
                                'filter' => [
                                    '=TASK_ID' => $ID,
                                    '=STATUS' => \Abbyy\Cloud\TaskFileTable::STATUS_FAILED,
                                ]
                            ]);
                            while ($arFile = $rsFiles->fetch()) {
                                \Abbyy\Cloud\TaskFileTable::update($arFile['ID'], [
                                    'STATUS' => \Abbyy\Cloud\TaskFileTable::STATUS_NEW,
                                ]);
                            }
                        }


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
        "content" => GetMessage("ABBYY_CLOUD_PROFILQ"),
        "default" => true,
        //"sort" => "PROFILE_ID",
    ),
    array(
        "id" => "ELEMENT",
        "content" => GetMessage("ABBYY_CLOUD_ELEMENT"),
        "default" => true,
    ),
    array(
        "id" => "LANGUAGES",
        "content" => GetMessage("ABBYY_CLOUD_PEREVOD"),
        "default" => true,
    ),
    array(
        "id" => "ORDER_NUMBER",
        "content" => GetMessage("ABBYY_CLOUD_ZAKAZA"),
        "default" => true,
    ),
    array(
        "id" => "STATUS",
        "content" => GetMessage("ABBYY_CLOUD_STATUS"),
        "default" => true,
    ),
    array(
        "id" => "AMOUNT",
        "content" => GetMessage("ABBYY_CLOUD_STOIMOSTQ"),
        "default" => true,
    ),
    array(
        "id" => "DEADLINE",
        "content" => GetMessage("ABBYY_CLOUD_DEDLAYN"),
        "default" => true,
    ),
    array(
        "id" => "COMMENT",
        "content" => GetMessage("ABBYY_CLOUD_PRIMECANIE"),
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

foreach (\Abbyy\Cloud\TaskTable::getStatusList() as $sStatusID => $sStatusName) {
    $arStat[$sStatusName] = \Abbyy\Cloud\TaskTable::getCount(array_merge($filter, ['=STATUS' => $sStatusID]));
}

$rsItems = \Abbyy\Cloud\TaskTable::getList(array(
    'order' => array(strtoupper($by) => $order),
    'count_total' => true,
    'offset' => $nav->getOffset(),
    'limit' => $nav->getLimit(),
    'filter' => $filter,
));

$nav->setRecordCount($rsItems->getCount());

$lAdmin->setNavigation($nav, GetMessage("ABBYY_CLOUD_ZADANIA"));

$arTypes = \Abbyy\Cloud\ProfileTable::getTypeList();

while ($arItem = $rsItems->fetch()) {

    $arRow = [];
    $arRow['ID'] = $arItem['ID'];
    $arRow['COMMENT'] = $arItem['COMMENT'];
    $arRow['DEADLINE'] = $arItem['DEADLINE'] ? date('Y-m-d\\TH:i:s.0\\Z', $arItem['DEADLINE']->getTimestamp()) : '-';//$arItem['DEADLINE'];
    $arRow['AMOUNT'] = $arItem['AMOUNT'] . $arItem['CURRENY'];
    $arRow['STATUS'] = $arStatus[$arItem['STATUS']];
    $arRow['ORDER_NUMBER'] = $arItem['ORDER_NUMBER'] ?: '&mdash;';

    $arProfile = \Abbyy\Cloud\ProfileTable::getById($arItem['PROFILE_ID'])->fetch();
    $sProfileLink = '/bitrix/admin/abbyy.cloud_profile.php?ID=' . $arProfile['ID'] . '&lang=ru';
    $arRow['PROFILE'] = $arProfile['NAME'] . ' [<a href="' . $sProfileLink . '" target="_blank">' . $arItem['PROFILE_ID'] . '</a>]';

    $arIBlock = CIBlock::GetByID($arProfile['IBLOCK_ID'])->Fetch();

    $arElement = CIBlockElement::GetByID($arItem['ELEMENT_ID'])->Fetch();
    $sElementLink = '/bitrix/admin/iblock_element_edit.php?IBLOCK_ID=' . $arProfile['IBLOCK_ID'] . '&type=' . $arIBlock['IBLOCK_TYPE_ID'] . '&ID=10834&lang=ru&find_section_section=0&WF=Y';

    $arRow['ELEMENT'] = $arElement['NAME'] . ' [<a href="' . $sElementLink . '" target="_blank">' . $arItem['ELEMENT_ID'] . '</a>]';

    $rsFiles = \Abbyy\Cloud\TaskFileTable::getList([
        'filter' => [
            '=TASK_ID' => $arItem['ID'],
        ],
    ]);

    $arLang = [];
    while ($arFile = $rsFiles->fetch()) {
        $sLangRow = '<a href="/bitrix/admin/abbyy.cloud_content.php?lang=ru&TASK_ID=' . $arItem['ID'] . '" target="_blank">' . $arFile['LANG_FROM'] . '</a> -> ';
        if ($arFile['STATUS'] == \Abbyy\Cloud\TaskFileTable::STATUS_SUCCESS) {
            $sLangRow .= '<a href="/bitrix/admin/abbyy.cloud_content.php?lang=ru&FILE_ID=' . $arFile['ID'] . '" target="_blank">' . $arFile['LANG_TO'] . '</a>';
        } else {
            $sLangRow .= $arFile['LANG_TO'];
        }
        $arLang[] = $sLangRow;
    }
    $sType = $arTypes[$arItem['TYPE']];

    $arRow['LANGUAGES'] = $sType . ':<br>' . implode("<br>", $arLang);

    $row = &$lAdmin->AddRow($arRow['ID'], $arRow);

    $row->AddViewField('ELEMENT', $arRow['ELEMENT']);
    $row->AddViewField('LANGUAGES', $arRow['LANGUAGES']);
    $row->AddViewField('PROFILE', $arRow['PROFILE']);
    //$row->AddInputField('NAME', Array("size" => "20"));
    //$row->AddCheckField("ACTIVE");

    $arActions = [];

    //$arActions[] = array("ICON" => "edit", "TEXT" => "Редактировать", "ACTION" => $lAdmin->ActionRedirect("abbyy.cloud.php?ID=" . urlencode($arRow['ID'])), "DEFAULT" => true);

    foreach ($arStatus as $sStatus => $sLabel) {
        if ($sStatus == $arItem['STATUS']) continue;
        $arActions[] = array(
            "ICON" => "edit",
            "TEXT" => GetMessage("ABBYY_CLOUD_V_STATUS") . $sLabel . '"',
            "ACTION" => $lAdmin->ActionDoGroup($arRow['ID'], "status", "status_to_move={$sStatus}"),
        );
    }


    $arActions[] = array(
        "ICON" => "delete",
        "TEXT" => GetMessage("ABBYY_CLOUD_UDALITQ"),
        "ACTION" => "if(confirm('".GetMessage("ABBYY_CLOUD_UDALITQ_PROFILQ") . $lAdmin->ActionDoGroup($arRow['ID'], "delete")
    );

    $row->AddActions($arActions);
}


$arActions = array(
    "delete" => GetMessage("MAIN_ADMIN_LIST_DELETE"),
);
$arParams = array();

$statuses = '<div id="status_to_move" style="display:none"><select name="status_to_move">';
foreach ($arStatus as $sStatus => $sLabel) {
    $statuses .= '<option value="' . $sStatus . '">' . $sLabel . '</option>';
}
$statuses .= '</select></div>';

$arActions["status"] = GetMessage("ABBYY_CLOUD_IZMENITQ_STATUS");
$arActions["status_chooser"] = array("type" => "html", "value" => $statuses);

$arParams["select_onchange"] = "BX('status_to_move').style.display = (this.value == 'status' ? 'block':'none');";

$lAdmin->AddGroupActionTable($arActions, $arParams);


$lAdmin->CheckListMode();


require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_after.php");
?>
<form method="GET" name="find_form" id="find_form" action="<? echo $APPLICATION->GetCurPage() ?>">
    <?
    $arFindFields = Array();
    $arFindFields["STATUS"] = GetMessage("ABBYY_CLOUD_STATUS");

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
        <td><?=GetMessage("ABBYY_CLOUD_PROFILQ")?></td>
        <td>
            <select name="find_profile" id="find_profile">
                <option value=""><?=GetMessage("ABBYY_CLOUD_VSE")?></option>
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
        <td><?=GetMessage("ABBYY_CLOUD_STATUS")?></td>
        <td>
            <select name="find_status" id="find_status">
                <option value=""><?=GetMessage("ABBYY_CLOUD_VSE")?></option>
                <? foreach (\Abbyy\Cloud\TaskTable::getStatusList() as $sCode => $sStatus): ?>
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
             value="<?=GetMessage("ABBYY_CLOUD_NAYTI")?>"
             title="<?=GetMessage("ABBYY_CLOUD_NAYTI")?>" onClick="return applyFilter(this);">
    <input class="adm-btn" type="submit" name="del_filter"
           value="<?=GetMessage("ABBYY_CLOUD_OTMENITQ")?>"
           title="<?=GetMessage("ABBYY_CLOUD_OTMENITQ")?>"
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
