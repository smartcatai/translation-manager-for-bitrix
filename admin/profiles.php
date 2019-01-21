<?php
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php");
IncludeModuleLangFile(__FILE__);
global $APPLICATION;
$sModuleDir = dirname(dirname(__FILE__));
$sModuleId = basename($sModuleDir);

\Bitrix\Main\Loader::includeModule($sModuleId);

$APPLICATION->SetTitle(GetMessage("SMARTCAT_CONNECTOR_PROFILI_PEREVODA"));

$sTableID = "tbl_smartcat_connector_profiles";
$oSort = new CAdminSorting($sTableID, "name", "asc");
$lAdmin = new CAdminList($sTableID, $oSort);

if ($lAdmin->EditAction()) {
    foreach ($_REQUEST['FIELDS'] as $ID => $arFields) {
        if (!$lAdmin->IsUpdated($ID)) continue;
        $arFields['ACTIVE'] = ($arFields['ACTIVE'] == 'Y');
        \Smartcat\Connector\ProfileTable::update($ID, $arFields);
    }
}

if ($arID = $lAdmin->GroupAction()) {
    foreach ($arID as $ID) {
        if (strlen($ID) <= 0) continue;
        $ID = IntVal($ID);
        switch ($_REQUEST['action']) {
            case "delete":
                \Smartcat\Connector\ProfileTable::delete($ID);
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
        "id" => "NAME",
        "content" => GetMessage("SMARTCAT_CONNECTOR_NAZVANIE"),
        "default" => true,
        "sort" => "NAME",
    ),
    array(
        "id" => "INFOBLOCK",
        "content" => GetMessage("SMARTCAT_CONNECTOR_INFOBLOCK"),
        "default" => true,
        "sort" => "INFOBLOCK",
    ),
    array(
        "id" => "VENDOR",
        "content" => GetMessage("SMARTCAT_CONNECTOR_VENDOR"),
        "default" => true,
    ),
    array(
        "id" => "LANG",
        "content" => GetMessage("SMARTCAT_CONNECTOR_AZYK_ORIG"),
        "default" => true,
    ),
    array(
        "id" => "LANGS",
        "content" => GetMessage("SMARTCAT_CONNECTOR_AZYKI"),
        "default" => true,
    ),
    array(
        "id" => "ACTIVE",
        "content" => GetMessage("SMARTCAT_CONNECTOR_AKTIVNOSTQ"),
        "default" => true,
    ),
);


$lAdmin->AddHeaders($arHeader);

$nav = new \Bitrix\Main\UI\AdminPageNavigation("nav-profiles");

$rsItems = \Smartcat\Connector\ProfileTable::getList(array(
    'order' => array(strtoupper($by) => $order),
    'count_total' => true,
    'offset' => $nav->getOffset(),
    'limit' => $nav->getLimit(),
));

$nav->setRecordCount($rsItems->getCount());

$lAdmin->setNavigation($nav, GetMessage("SMARTCAT_CONNECTOR_PROFILI"));

$arTypes = \Smartcat\Connector\ProfileTable::getTypeList();

while ($arItem = $rsItems->fetch()) {

    $arIblockFrom = CIBlock::GetByID($arItem['IBLOCK_ID'])->Fetch();
    $arRow = [];
    $arRow['ID'] = $arItem['ID'];
    $arRow['NAME'] = $arItem['NAME'];
    $arRow['INFOBLOCK'] = $arIblockFrom['NAME'];
    $arRow['ACTIVE'] = $arItem['ACTIVE'];

    $arRow['LANG'] = $arItem['LANG'];
    $arRow['LANGS'] = [];
    $arIBlocks = \Smartcat\Connector\ProfileIblockTable::getList([
        'filter' => [
            '=PROFILE_ID' => $arRow['ID'],
        ]
    ])->fetchAll();

    foreach ($arIBlocks as $arIBlock) {
        $arRow['LANGS'][] = $arIBlock['LANG'];
    }
    $arRow['LANGS'] = implode(', ', $arRow['LANGS']);

    // $arRow['TASKS_COUNT'] = \Smartcat\Connector\TaskTable::getCount([
    //     'PROFILE_ID' => $arRow['ID'],
    // ]);

    $arRow['VENDOR'] = explode('|', $arItem['VENDOR'])[1];

    $row = &$lAdmin->AddRow($arRow['ID'], $arRow);

    $row->AddInputField('NAME', Array("size" => "20"));
    $row->AddCheckField("ACTIVE");

    $arActions = [];

    $arActions[] = array("ICON" => "edit", "TEXT" => GetMessage("SMARTCAT_CONNECTOR_REDAKTIROVATQ"), "ACTION" => $lAdmin->ActionRedirect("smartcat.connector_profile.php?ID=" . urlencode($arRow['ID'])), "DEFAULT" => true);

    $arActions[] = array(
        "ICON" => "delete",
        "TEXT" => GetMessage("SMARTCAT_CONNECTOR_UDALITQ"),
        "ACTION" => "if(confirm('".GetMessage("SMARTCAT_CONNECTOR_UDALITQ_PROFILQ") . $lAdmin->ActionDoGroup($arRow['ID'], "delete")
    );

    $row->AddActions($arActions);
}

$aContext = array(
    array(
        "ICON" => "btn_new",
        "TEXT" => GetMessage("SMARTCAT_CONNECTOR_DOBAVITQ_PROFILQ"),
        "ONCLICK" => "location.href = 'smartcat.connector_profile.php'",
        "TITLE" => GetMessage("SMARTCAT_CONNECTOR_DOBAVITQ_PROFILQ"),
    ),
);

$lAdmin->AddAdminContextMenu($aContext);

$lAdmin->AddGroupActionTable(Array(
    "delete" => GetMessage("MAIN_ADMIN_LIST_DELETE"),
));

$lAdmin->CheckListMode();


require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_after.php");
?>

<? $lAdmin->DisplayList(); ?>

<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_admin.php"); ?>
