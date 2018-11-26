<?php
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php");
IncludeModuleLangFile(__FILE__);
global $APPLICATION;
$sModuleDir = dirname(dirname(__FILE__));
$sModuleId = basename($sModuleDir);

\Bitrix\Main\Loader::includeModule($sModuleId);


$arSite = CSite::GetList($by = "sort", $order = "asc", ['DEFAULT' => 'Y'])->Fetch();
if ($arSite) {
    $arTemplate = CSite::GetTemplateList($arSite['ID'])->Fetch();
}

$arAddStyles = [];

if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/bitrix/templates/' . $arTemplate['TEMPLATE'] . '/template_styles.css')) {
    $arAddStyles[] = '/bitrix/templates/' . $arTemplate['TEMPLATE'] . '/template_styles.css';
}

if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/local/templates/' . $arTemplate['TEMPLATE'] . '/template_styles.css')) {
    $arAddStyles[] = '/local/templates/' . $arTemplate['TEMPLATE'] . '/template_styles.css';
}

if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/bitrix/templates/' . $arTemplate['TEMPLATE'] . '/styles.css')) {
    $arAddStyles[] = '/bitrix/templates/' . $arTemplate['TEMPLATE'] . '/styles.css';
}

if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/local/templates/' . $arTemplate['TEMPLATE'] . '/styles.css')) {
    $arAddStyles[] = '/local/templates/' . $arTemplate['TEMPLATE'] . '/styles.css';
}

$sAddCSS = \Bitrix\Main\Config\Option::get($sModuleId, 'add_css');

if ($sAddCSS) {
    $arAddStyles[] = $sAddCSS;
}

$TASK_ID = intval($_REQUEST['TASK_ID']);
$FILE_ID = intval($_REQUEST['FILE_ID']);

$arTask = null;
$arFile = null;

if ($TASK_ID > 0) {
    $arTask = \Smartcat\Connector\TaskTable::getById($TASK_ID)->fetch();
    if ($arTask) {
        $arPofile = \Smartcat\Connector\ProfileTable::getById($arTask['PROFILE_ID'])->fetch();
    }
} elseif ($FILE_ID > 0) {
    $arFile = \Smartcat\Connector\TaskFileTable::getById($FILE_ID)->fetch();
    if ($arFile) {
        $arTask = \Smartcat\Connector\TaskTable::getById($arFile['TASK_ID'])->fetch();
    }
    if ($arTask) {
        $arPofile = \Smartcat\Connector\ProfileTable::getById($arTask['PROFILE_ID'])->fetch();
    }
}

$APPLICATION->SetTitle($arPofile['NAME']);


//require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_after.php");
?>
<html lang="ru">
<head>
    <? foreach ($arAddStyles as $sStyle): ?>
        <link rel="stylesheet" href="<?= $sStyle; ?>">
    <? endforeach; ?>
    <style type="text/css">
        field {
            display: block;
            margin: 0 0 1em 0;
            padding: 0 0 1em 0;
            border-bottom: 1px dotted #ccc;
        }

        body {
            background: #333;
        }

        .smartcat-content {
            width: 100%;
            max-width: 1200px;
            margin: 2em auto;
            background: #fff;
            padding: 15px 20px;
            border-radius: 10px;
        }

        .smartcat-content img {
            max-width: 100%;
        }

        .smartcat-content * {
            margin-left: 0 !important;
            margin-right: 0 !important;
            max-width: 100% !important;
        }

    </style>
    <title><?= $APPLICATION->ShowTitle(); ?></title>
</head>
<? if (!$arPofile): ?>
    <? CAdminMessage::ShowMessage(GetMessage("SMARTCAT_CONNECTOR_TEKST_NE_NAYDEN")); ?>
<? else: ?>

    <div class="smartcat-content">
        <? if ($arFile): ?>
            <?= $arFile['TRANSLATION']; ?>
        <? elseif ($arTask): ?>
            <?= $arTask['CONTENT']; ?>
        <? endif; ?>
    </div>
<? endif; ?>
</html>
<?
require($_SERVER["DOCUMENT_ROOT"] . BX_ROOT . "/modules/main/include/epilog_admin_js.php");
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_after.php"); ?>
