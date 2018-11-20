<?php
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php");

use \Smartcat\Connector\Helper\ApiHelper;

IncludeModuleLangFile(__FILE__);
global $APPLICATION;

CJSCore::Init(['jquery']);

$sModuleDir = dirname(dirname(__FILE__));
$sModuleId = basename($sModuleDir);

\Bitrix\Main\Loader::includeModule($sModuleId);
\Bitrix\Main\Loader::includeModule('iblock');

try{
    $acc_info = \Smartcat\Connector\Helper\ApiHelper::getAccount();
}catch(\Exception $e){
    require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_after.php");
    CAdminMessage::ShowMessage(GetMessage("SMARTCAT_CONNECTOR_ACCOUNT_ERROR") .': '. $e->getMessage() . '<br>' .GetMessage("SMARTCAT_CONNECTOR_ACCOUNT_ERROR_EXPLAIN")  );
    require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_admin.php");
}

/**
 *  Language code
 */
$arLanguages = \Smartcat\Connector\Helper\LangHelper::getLanguages();

$arLanguagesFrom = [];

$rsSiteLangs = CLanguage::GetList(($by = 'id'), ($sort = 'asc'));
while ($arLang = $rsSiteLangs->Fetch()) {
    if (array_key_exists($arLang['LANGUAGE_ID'], $arLanguages)) {
        $arLanguagesFrom[$arLang['LANGUAGE_ID']] = $arLanguages[$arLang['LANGUAGE_ID']];
    }
}

$arWorflowStages = ApiHelper::getWorkflowStages();
$arVendors = ApiHelper::getVendor();

$arVendors[0] = GetMessage("SMARTCAT_CONNECTOR_WITHOUT_VENDOR");

$arFieldsToTranslate = [
    'NAME' => GetMessage("SMARTCAT_CONNECTOR_NAZVANIE"),
    'PREVIEW_TEXT' => GetMessage("SMARTCAT_CONNECTOR_OPISANIE_DLA_ANONSA"),
    'DETAIL_TEXT' => GetMessage("SMARTCAT_CONNECTOR_DETALQNOE_OPISANIE"),
    'IBLOCK_SECTION_ID' => GetMessage("SMARTCAT_CONNECTOR_RAZDEL_INFOBLOKA"),
];

$arPropsToTranslate = [];

$arIblockTree = [];

$rsTypes = CIBlockType::GetList(['name' => 'asc']);
while ($arType = $rsTypes->Fetch()) {
    $arIblockTree[$arType['ID']] = [
        'NAME' => $arType['NAME'],
        'IBLOCK' => [],
    ];

    $rsIblocks = CIBlock::GetList(['NAME' => 'asc'], ['TYPE' => $arType['ID']]);
    while ($arIblock = $rsIblocks->Fetch()) {
        $arIblockTree[$arType['ID']]['IBLOCK'][$arIblock['ID']] = $arIblock['NAME'];
    }
    if (empty($arIblockTree[$arType['ID']]['IBLOCK'])) {

    }
}

$arErrors = [];

$ID = intval($_REQUEST['ID']);

if ($ID > 0) {


    $arProfile = \Smartcat\Connector\ProfileTable::getById($ID)->fetch();
    $APPLICATION->SetTitle($arProfile['NAME']);

    if ($arProfile) {
        $arProfileIblock = \Smartcat\Connector\ProfileIblockTable::getList([
            'filter' => [
                'PROFILE_ID' => $arProfile['ID'],
            ],
        ])->fetchAll();

        if (!is_array($arProfile['FIELDS']['FIELDS'])) {
            $arProfile['FIELDS']['FIELDS'] = [];
        }

        if (!is_array($arProfile['FIELDS']['PROPS'])) {
            $arProfile['FIELDS']['PROPS'] = [];
        }

    } else {
        require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_after.php");
        CAdminMessage::ShowMessage(GetMessage("SMARTCAT_CONNECTOR_PROFILQ_NE_NAYDEN"));
        require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_admin.php");
    }

} else {

    /*if ($_REQUEST['IBLOCK_ID'] > 0) {
        $arProfile = \Smartcat\Connector\ProfileTable::getList([
            'filter' => [
                '=IBLOCK_ID' => intval($_REQUEST['IBLOCK_ID']),
            ],
        ])->fetch();
        if ($arProfile) {
            LocalRedirect($APPLICATION->GetCurPageParam('ID=' . $arProfile['ID'] . '&IBLOCK_ID=' . intval($_REQUEST['IBLOCK_ID']), ['ID', 'IBLOCK_ID']));
        }
    }*/


    $arProfile = [
        'FIELDS' => [
            'FIELDS' => [],
            'PROPS' => [],
        ],
    ];

    $arProfileIblock = [];
}

if ($_REQUEST['IBLOCK_ID'] > 0) {
    $arProfile['IBLOCK_ID'] = intval($_REQUEST['IBLOCK_ID']);
}

if (!empty($_REQUEST['LANG']) && array_key_exists($_REQUEST['LANG'], $arLanguagesFrom)) {
    $arProfile['LANG'] = $_REQUEST['LANG'];
}

if (empty($arProfile['LANG'])) $arProfile['LANG'] = reset(array_keys($arLanguagesFrom));

$arLanguagesTo = [];

if (!empty($arProfile['LANG'])) {


    $langs = \Smartcat\Connector\Helper\ApiHelper::getLanguages();

    $arLang = [];

    if (is_array($langs)) {
        foreach ($langs as $lang) {
            $langTo = $lang->getName();
            if (array_key_exists($langTo, $arLanguages)) {
                $arLanguagesTo[$langTo] = $arLanguages[$langTo];
            }
        }
    }

    if (empty($arLanguagesTo)) {
        $arErrors[] = GetMessage("SMARTCAT_CONNECTOR_NET_DOSTUPNYH_AZYKOV");
    }

}


if ($arProfile['IBLOCK_ID'] > 0) {
    $rsProps = CIBlockProperty::GetList(['NAME' => 'asc'], [
        'IBLOCK_ID' => $arProfile['IBLOCK_ID'],
        'PROPERTY_TYPE' => 'S',
    ]);

    while ($arProp = $rsProps->Fetch()) {
        $arPropsToTranslate[$arProp['CODE']] = $arProp['NAME'];
    }
}

if ($_SERVER['REQUEST_METHOD'] == "POST" && check_bitrix_sessid()) {

    //echo '<pre>' . print_r($_POST, true) . '</pre>';

    $CIBlock = new CIBlock();
    $CIBlockType = new CIBlockType();
    $CIBlockElement = new CIBlockElement();

    if ($_REQUEST['IBLOCK_ID'] <= 0) {
        $arErrors[] = GetMessage("SMARTCAT_CONNECTOR_NE_UKAZAN_INFOBLOK_D");
    }

    $arIblockFrom = null;
    if (empty($arErrors)) {
        $arIblockFrom = CIBlock::GetByID($_REQUEST['IBLOCK_ID'])->Fetch();
    }

    if (!$arIblockFrom) {
        $arErrors[] = GetMessage("SMARTCAT_CONNECTOR_NE_UDALOSQ_NAYTI_INF");
    }

    if($_REQUEST['WORKFLOW']){
        $workflow = $_REQUEST['WORKFLOW'];
        foreach($workflow as $id=>$stage){
            if(!in_array($stage, $arWorflowStages)){
                array_splice($workflow, $id, 1);
            }
        }
    }

    $arProfile['NAME'] = $arIblockFrom['NAME'];
    $arProfile['ACTIVE'] = (isset($_REQUEST['ACTIVE']) && $_REQUEST['ACTIVE'] == 'Y');
    $arProfile['PUBLISH'] = (isset($_REQUEST['PUBLISH']) && $_REQUEST['PUBLISH'] == 'Y');
    $arProfile['AUTO_ORDER'] = (isset($_REQUEST['AUTO_ORDER']) && $_REQUEST['AUTO_ORDER'] == 'Y');
    $arProfile['IBLOCK_ID'] = intval($_REQUEST['IBLOCK_ID']);
    $arProfile['LANG'] = trim($_REQUEST['LANG']);
    $arProfile['FIELDS'] = $_REQUEST['FIELDS'];
    $arProfile['WORKFLOW'] = $workflow ? implode(',',$workflow) : '';
    $arProfile['VENDOR'] = $_REQUEST['VENDOR'];

    if (empty($arErrors)) {
        if ($ID > 0) {
            $result = \Smartcat\Connector\ProfileTable::update($ID, $arProfile);
            if ($result->isSuccess()) {
                $arProfile['ID'] = $ID;
            } else {
                $arErrors[] = GetMessage("SMARTCAT_CONNECTOR_NE_UDALOSQ_OBNOVITQ") . implode('<br>', $result->getErrorMessages());
            }
        } else {
            $result = \Smartcat\Connector\ProfileTable::add($arProfile);
            if ($result->isSuccess()) {
                $arProfile['ID'] = $result->getId();
            } else {
                $arErrors[] = GetMessage("SMARTCAT_CONNECTOR_NE_UDALOSQ_SOZDATQ_P") . implode('<br>', $result->getErrorMessages());
            }
        }

        foreach ($_REQUEST['IBLOCKS'] as $sIBlockID => $arIblockData) {
            $isNew = ($sIBlockID[0] == 'n');

            if (!$isNew && $arIblockData['REMOVE'] == 'Y') {
                \Smartcat\Connector\ProfileIblockTable::delete($sIBlockID);
                continue;
            }

            if ($arIblockData['LANG'] == $arProfile['LANG']) continue;

            unset($arIblockData['REMOVE']);

            if (empty($arIblockData['LANG'])) continue;

            if (empty($arIblockData['IBLOCK_ID'])) {
                try {
                    $arIblockData['IBLOCK_ID'] = \Smartcat\Connector\Helper\IblockHelper::createIBForLang($arProfile['IBLOCK_ID'], $arIblockData['LANG']);
                } catch (\Exception $e) {
                    $arErrors[] = $e->getMessage();
                    continue;
                }
                \Smartcat\Connector\Helper\IblockHelper::copyIBlockProps($arProfile['IBLOCK_ID'], $arIblockData['IBLOCK_ID']);

            } else {
                $arTargetIBlock = CIBlock::GetByID($arIblockData['IBLOCK_ID'])->Fetch();
                if (!$arTargetIBlock) {
                    $arErrors[] = GetMessage("SMARTCAT_CONNECTOR_NE_UDALOSQ_NAYTI_INF1") . $arIblockData['IBLOCK_ID'] . '"';
                    continue;
                }
            }


            $arProfileIblockFields = $arIblockData;
            $arProfileIblockFields['PROFILE_ID'] = $arProfile['ID'];
            var_dump($arProfile['ID'],$arErrors);

            if ($isNew) {
                $res = \Smartcat\Connector\ProfileIblockTable::add($arProfileIblockFields);
            } else {
                $res = \Smartcat\Connector\ProfileIblockTable::update($sIBlockID, $arProfileIblockFields);
            }

            if (!$res->isSuccess()) {
                echo '<pre>' . print_r($res->getErrorMessages(), true) . '</pre>';
                die();
            }


        }

    }


    if (empty($arErrors)) {
        if ($_REQUEST['apply']) {
            LocalRedirect($APPLICATION->GetCurPageParam('ID=' . $arProfile['ID'], ['ID']));
        } else {
            LocalRedirect('smartcat.connector_profiles.php');
        }
        /*} else {
            echo '<pre>' . print_r($arProfile, true) . '</pre>';
            echo '<pre>' . print_r($arIblockData, true) . '</pre>';
            echo '<pre>' . print_r($arErrors, true) . '</pre>';
            die();*/
    }
}


require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_after.php");

$aMenu = array(
    array(
        "TEXT" => GetMessage("SMARTCAT_CONNECTOR_PROFILI_PEREVODA"),
        "LINK" => "smartcat.connector_profiles.php?lang=" . LANGUAGE_ID,
        "TITLE" => GetMessage("SMARTCAT_CONNECTOR_PROFILI_PEREVODA"),
    ),
);

$context = new CAdminContextMenu($aMenu);
$context->Show();


$aTabs = array(
    array(
        "DIV" => "edit1",
        "TAB" => GetMessage("SMARTCAT_CONNECTOR_PROFILQ_PEREVODA"),
        "ICON" => "site_edit",
        "TITLE" => GetMessage("SMARTCAT_CONNECTOR_PROFILQ_PEREVODA"),
    ),
);
$tabControl = new CAdminTabControl("tabControl", $aTabs);

?>
<?
$bNeedAuth = false;

if (!empty($arErrors)): ?>
    <? foreach ($arErrors as $sError):
        if ($sError == 'Unauthorized') {
            $bNeedAuth = true;
            continue;
        }
        ?>
        <? CAdminMessage::ShowMessage($sError); ?>
    <? endforeach; ?>
<? endif; ?>

<? if (!$bNeedAuth): ?>

    <form method="POST" action="<? echo $APPLICATION->GetCurPageParam() ?>" name="bform">
        <?= bitrix_sessid_post() ?>
        <input type="hidden" name="lang" value="<? echo LANG ?>">
        <input type="hidden" name="ID" value="<?= $ID; ?>">
        <?
        $tabControl->Begin();
        $tabControl->BeginNextTab();
        ?>

        <tr>
            <th><?= GetMessage("SMARTCAT_CONNECTOR_INFOBLOK") ?></th>
            <td>
                <select name="IBLOCK_ID" required class="js-select-iblock">
                    <option value="">[<?= GetMessage("SMARTCAT_CONNECTOR_VYBRATQ") ?></option>
                    <? foreach ($arIblockTree as $arType): ?>
                        <optgroup label="<?= $arType['NAME']; ?>">
                            <? foreach ($arType['IBLOCK'] as $iIblockID => $sIblockName): ?>
                                <option value="<?= $iIblockID; ?>"
                                        data-url="<?= $APPLICATION->GetCurPageParam('IBLOCK_ID=' . $iIblockID, ['IBLOCK_ID']); ?>"
                                    <?= ($arProfile['IBLOCK_ID'] == $iIblockID ? 'selected' : ''); ?>
                                >
                                    <?= $sIblockName; ?>
                                </option>
                            <? endforeach; ?>
                        </optgroup>
                    <? endforeach; ?>
                </select>
            </td>
        </tr>

        <tr>
            <td><?= GetMessage("SMARTCAT_CONNECTOR_AKTIVNOSTQ") ?></td>
            <td><input type="checkbox" name="ACTIVE" value="Y" <?= ($arProfile['ACTIVE'] == 'Y' ? 'checked' : ''); ?>>
            </td>
        </tr>

        <tr>
            <td><?= GetMessage("SMARTCAT_CONNECTOR_AZYK") ?></td>

            <td>
                <select name="LANG" class="js-select-lang">
                    <? foreach ($arLanguagesFrom as $sLang => $sLangName): ?>
                        <option
                                value="<?= $sLang; ?>" <?= ($arProfile['LANG'] == $sLang ? 'selected' : ''); ?>
                                data-url="<?= $APPLICATION->GetCurPageParam('LANG=' . $sLang, ['LANG']); ?>"
                        >
                            <?= $sLangName; ?> [<?= $sLang; ?>]
                        </option>
                    <? endforeach; ?>
                </select>
            </td>
        </tr>
        <tr class="heading">
            <td colspan="2"><?= GetMessage("SMARTCAT_CONNECTOR_PARAMETRY_PEREVODA") ?></td>
        </tr>

        <tr>
            <td><?= GetMessage("SMARTCAT_CONNECTOR_VENDOR") ?></td>
            <td>
                <select name="VENDOR" class="js-select-lang">
                    <? foreach ($arVendors as $id=> $name): ?>
                        <? $vendorFull = $id . '|' . $name; ?>
                        <option value="<?= $vendorFull; ?>" <?= ($arProfile['VENDOR'] === $vendorFull ? 'selected' : ''); ?> >
                            <?= $name; ?>
                        </option>
                    <? endforeach; ?>
                </select>
            </td>
        </tr>

        <tr>
            <td><?= GetMessage("SMARTCAT_CONNECTOR_TIP_PEREVODA") ?></td>
            <td>
                    <? foreach ($arWorflowStages as $stage): ?>
                        <label>
                            <input type="checkbox" name="WORKFLOW[]" value="<?= $stage; ?>" <?= (strpos($arProfile['WORKFLOW'],$stage) !== false ? 'checked' : ''); ?>>
                            <?= $stage; ?>
                        </label><br><br>
                    <? endforeach; ?>
            </td>
        </tr>

        <tr>
            <td><?= GetMessage("SMARTCAT_CONNECTOR_PUBLIKOVATQ_PEREVOD") ?></td>
            <td><input type="checkbox" name="PUBLISH" value="Y" <?= ($arProfile['PUBLISH'] == 'Y' ? 'checked' : ''); ?>>
            </td>
        </tr>

        <tr>
            <td><?= GetMessage("SMARTCAT_CONNECTOR_AVTOMATICESKI_PEREVO") ?></td>
            <td><input type="checkbox" name="AUTO_ORDER"
                       value="Y" <?= ($arProfile['AUTO_ORDER'] == 'Y' ? 'checked' : ''); ?>>
            </td>
        </tr>

        <tr class="heading">
            <td colspan="2"><?= GetMessage("SMARTCAT_CONNECTOR_KAKIE_POLA_PEREVODIT") ?></td>
        </tr>

        <? foreach ($arFieldsToTranslate as $sFieldCode => $sFieldTitle): ?>
            <tr>
                <td>&nbsp;</td>
                <td>
                    <label>
                        <input type="checkbox" name="FIELDS[FIELDS][]" value="<?= $sFieldCode; ?>"
                            <?= (in_array($sFieldCode, $arProfile['FIELDS']['FIELDS']) ? 'checked' : ''); ?>> <?= $sFieldTitle; ?>
                    </label>
                </td>
            </tr>

        <? endforeach; ?>

        <? foreach ($arPropsToTranslate as $sFieldCode => $sFieldTitle): ?>
            <tr>
                <td>&nbsp;</td>
                <td>
                    <label>
                        <input type="checkbox" name="FIELDS[PROPS][]" value="<?= $sFieldCode; ?>"
                            <?= (in_array($sFieldCode, $arProfile['FIELDS']['PROPS']) ? 'checked' : ''); ?>> <?= $sFieldTitle; ?>
                    </label>
                </td>
            </tr>

        <? endforeach; ?>

        <tr class="heading">
            <td colspan="2"><?= GetMessage("SMARTCAT_CONNECTOR_NA_KAKIE_AZYKI_PEREV") ?></td>
        </tr>

        <tr>
            <td colspan="2">
                <table class="adm-detail-content-table list-table adm-profile-iblock-table js-iblock-table">
                    <thead>
                    <tr class="heading">
                        <td><?= GetMessage("SMARTCAT_CONNECTOR_AZYK") ?></td>
                        <td><?= GetMessage("SMARTCAT_CONNECTOR_INFOBLOK") ?></td>
                        <td>&nbsp;</td>
                    </tr>
                    </thead>
                    <tbody id="js_iblock_rows">
                    <? foreach ($arProfileIblock as $arIBlock): ?>
                        <tr class="js-iblock-row">
                            <td>
                                <select name="IBLOCKS[<?= $arIBlock['ID']; ?>][LANG]">
                                    <? foreach ($arLanguagesTo as $sLang => $sLangName): ?>
                                        <option
                                                value="<?= $sLang; ?>" <?= ($arIBlock['LANG'] == $sLang ? 'selected' : ''); ?>>
                                            <?= $sLangName; ?> [<?= $sLang; ?>]
                                        </option>
                                    <? endforeach; ?>
                                </select>
                            </td>
                            <td>
                                <select name="IBLOCKS[<?= $arIBlock['ID']; ?>][IBLOCK_ID]">
                                    <option value="">[<?= GetMessage("SMARTCAT_CONNECTOR_SOZDATQ_NOVYY") ?></option>
                                    <? foreach ($arIblockTree as $arType): ?>
                                        <optgroup label="<?= $arType['NAME']; ?>">
                                            <? foreach ($arType['IBLOCK'] as $iIblockID => $sIblockName): ?>
                                                <? if ($iIblockID == $arProfile['IBLOCK_ID']) continue; ?>
                                                <option value="<?= $iIblockID; ?>"
                                                    <?= ($arIBlock['IBLOCK_ID'] == $iIblockID ? 'selected' : ''); ?>
                                                >
                                                    <?= $sIblockName; ?>
                                                </option>
                                            <? endforeach; ?>
                                        </optgroup>
                                    <? endforeach; ?>
                                </select>
                            </td>

                            <td>
                                <a href="#" class="adm-btn js-row-remove stored remove">&times;</a>
                                <input type="hidden" name="IBLOCKS[<?= $arIBlock['ID']; ?>][REMOVE]" value="N"
                                       class="js-hidden-remove">
                            </td>
                        </tr>
                    <? endforeach; ?>
                    <tr class="js-iblock-row">
                        <td>
                            <select name="IBLOCKS[n0][LANG]">
                                <option value="">[<?= GetMessage("SMARTCAT_CONNECTOR_VYBRATQ_AZYK") ?></option>
                                <? foreach ($arLanguagesTo as $sLang => $sLangName): ?>
                                    <option value="<?= $sLang; ?>">
                                        <?= $sLangName; ?> [<?= $sLang; ?>]
                                    </option>
                                <? endforeach; ?>
                            </select>
                        </td>
                        <td>
                            <select name="IBLOCKS[n0][IBLOCK_ID]">
                                <option value="">[<?= GetMessage("SMARTCAT_CONNECTOR_SOZDATQ_NOVYY") ?></option>
                                <? foreach ($arIblockTree as $arType): ?>
                                    <optgroup label="<?= $arType['NAME']; ?>">
                                        <? foreach ($arType['IBLOCK'] as $iIblockID => $sIblockName): ?>
                                            <? if ($iIblockID == $arProfile['IBLOCK_ID']) continue; ?>
                                            <option value="<?= $iIblockID; ?>">
                                                <?= $sIblockName; ?>
                                            </option>
                                        <? endforeach; ?>
                                    </optgroup>
                                <? endforeach; ?>
                            </select>
                        </td>

                        <td>
                            <a href="#" class="adm-btn js-row-remove remove">&times;</a>
                        </td>
                    </tr>
                    </tbody>
                </table>

            </td>
        </tr>

        <tr>
            <td colspan="2" align="center">
                <br>
                <a href="#" class="adm-btn adm-btn-add"
                   id="js_iblock_row_add"><?= GetMessage("SMARTCAT_CONNECTOR_DOBAVITQ_INFOBLOK") ?></a>
            </td>
        </tr>

        <?
        $tabControl->Buttons(array("back_url" => "smartcat.connector_profiles.php?lang=" . LANGUAGE_ID));
        $tabControl->End();
        $tabControl->ShowWarnings("bform", $message);
        ?>
    </form>

    <script>

        $(function () {

            var new_rows_count = 1;

            $('.js-select-iblock').on('change', function () {
                var url = $('option:selected', this).data('url');
                if (url) {
                    document.location = url;
                }
            });

            $('.js-select-lang').on('change', function () {
                var url = $('option:selected', this).data('url');
                if (url) {
                    document.location = url;
                }
            });

            $('#js_iblock_row_add').on('click', function (e) {
                e.preventDefault();
                var rows = $('.js-iblock-row'),
                    row_new = rows.last().clone();

                new_rows_count++;

                row_new.find('select').val('');
                $('#js_iblock_rows').append(row_new);

                row_new.find('select, input').each(function () {
                    var name = $(this).attr('name'),
                        replace = 'n' + (new_rows_count - 1);

                    name = name.replace(/\[n[0-9]+\]/, '[n' + (new_rows_count - 1) + ']');
                    console.log(name);
                    $(this).attr('name', name);
                });

                return false;
            });

            $('.js-iblock-table').on('click', '.js-row-remove', function (e) {
                e.preventDefault();
                var rows = $('.js-iblock-row'),
                    row = $(this).closest('.js-iblock-row');
                if (rows.length > 1) {
                    if ($(this).hasClass('stored')) {
                        row.toggleClass('removing');
                    } else {
                        $(this).closest('.js-iblock-row').remove();
                    }
                } else {
                    if ($(this).hasClass('stored')) {
                        row.toggleClass('removing');
                    } else {
                        rows.find('select').val('');
                    }
                }

                if (row.hasClass('removing')) {
                    $('.js-hidden-remove', row).val('Y');
                } else {
                    $('.js-hidden-remove', row).val('N');
                }

                return false;
            });

        });

    </script>

    <style type="text/css">
        .adm-profile-iblock-table td,
        .adm-profile-iblock-table th {
            width: 50%;
            padding: 0 5px 10px 5px;
        }

        .adm-profile-iblock-table td select {
            width: 100% !important;
        }

        .adm-profile-iblock-table .removing td select,
        .adm-profile-iblock-table .removing td input {
            opacity: 0.4;
        }

    </style>
<? else: ?>
    <? echo BeginNote(); ?>
    <?= GetMessage("SMARTCAT_CONNECTOR_NE_UDALOSQ_PODKLUCIT") ?>
    <a
            href="/bitrix/admin/settings.php?lang=<?= LANGUAGE_ID; ?>&mid=smartcat.connector&mid_menu=1"><?= GetMessage("SMARTCAT_CONNECTOR_NASTROYKI_DO") ?></a> <?= GetMessage("SMARTCAT_CONNECTOR_K_SERVISA") ?><? echo EndNote(); ?>
<? endif; ?>

<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_admin.php"); ?>
