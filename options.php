<?
use PhpParser\Node\Expr\Array_;

//if (!$USER->IsAdmin())
//    return;
IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/options.php");
IncludeModuleLangFile(__FILE__);

$module_id = "smartcat.connector";

$arErrors = Array();
$arMessages = Array();

\Bitrix\Main\Loader::includeModule($module_id);

$MOD_RIGHT = $APPLICATION->GetGroupRight($module_id);
$schema = new \Smartcat\Connector\Schema(dirname(__FILE__) . '/install/db/mysql');

if ($schema->needUpgrade() && $_REQUEST['db_upgrade'] == 'y') {
    $schema->upgrade();
    LocalRedirect($APPLICATION->GetCurPageParam('', array('db_upgrade')));
}

$arAllOptions = Array();


$arAllOptions[] = GetMessage("SMARTCAT_CONNECTOR_DOSTUP_K");
$arAllOptions[] = Array("api_id", "App ID", '', Array('text', 100));
$arAllOptions[] = Array("api_secret", "Api token", '', Array('text', 100));
$arAllOptions[] = Array("api_server", "Server", \SmartCat\Client\SmartCat::SC_EUROPE, Array('selectbox', Array(
    \SmartCat\Client\SmartCat::SC_ASIA => 'Asia',
    \SmartCat\Client\SmartCat::SC_EUROPE => 'Europe',
    \SmartCat\Client\SmartCat::SC_USA => 'USA',
)));
$arAllOptions[] = Array("api_test", "Test mode", '', Array('checkbox', false));



$arAllOptions[] = Array("note" => GetMessage("SMARTCAT_CONNECTOR_VERSIA_SHEMY") . $schema->getCurrentVersion() . ' ' . ($schema->needUpgrade() ? '<a href="' . $APPLICATION->GetCurPageParam('db_upgrade=y') . '">'.GetMessage("SMARTCAT_CONNECTOR_OBNOVITQ_DO") . $schema->getLastVersion() . '</a>' : ''));

if ($REQUEST_METHOD == 'POST' && strlen($Update) > 0 && check_bitrix_sessid()) {
    $arOptions = $arAllOptions;

    foreach ($arOptions as $option) {
        if (!is_array($option) || isset($option['note']))
            continue;

        $name = $option[0];
        $val = ${$name};
        if ($option[3][0] == 'checkbox' && $val != 'Y')
            $val = 'N';
        if ($option[3][0] == 'multiselectbox')
            $val = @implode(',', $val);

        \Bitrix\Main\Config\Option::set($module_id, $name, $val);
    }
    LocalRedirect($APPLICATION->GetCurPageParam());
}
$arAllOptions[] = GetMessage("SMARTCAT_CONNECTOR_ACCOUNT");
try{
    $acc_info = \Smartcat\Connector\Helper\ApiHelper::getAccount();
    if($acc_info){
        $arAllOptions[] = Array('note' => $acc_info->getName());
    }
}catch(\Exception $e){
    $apiId = \Bitrix\Main\Config\Option::get('smartcat.connector', 'api_id');
    $apiSecret = \Bitrix\Main\Config\Option::get('smartcat.connector', 'api_secret');
    if(!empty($apiId) || !empty($apiSecret) ){
        CAdminMessage::ShowMessage(GetMessage("SMARTCAT_CONNECTOR_ACCOUNT_ERROR") .': '. $e->getMessage());
    }
}

foreach ($arErrors as $strError)
    CAdminMessage::ShowMessage($strError);
foreach ($arMessages as $strMessage)
    CAdminMessage::ShowMessage(array("MESSAGE" => $strMessage, "TYPE" => "OK"));

?>
<?
$aTabs = array();
$aTabs[] = array('DIV' => 'set', 'TAB' => GetMessage('MAIN_TAB_SET'), 'ICON' => 'edit', 'TITLE' => GetMessage("SMARTCAT_CONNECTOR_NASTROYKI_MODULA"));


$tabControl = new CAdminTabControl("tabControl", $aTabs);

$tabControl->Begin();
?>
<form method="POST"
      action="<? echo $APPLICATION->GetCurPage() ?>?mid=<?= htmlspecialcharsbx($mid) ?>&lang=<?= LANGUAGE_ID ?>"
      name="mailchimp_settings">
    <? $tabControl->BeginNextTab(); ?>

    <? __AdmSettingsDrawList($module_id, $arAllOptions); ?>


    <? $tabControl->Buttons(); ?>
    <input type="submit" name="Update" <? if ($MOD_RIGHT < 'W') echo "disabled" ?>
           value="<? echo GetMessage('MAIN_SAVE') ?>">
    <input type="hidden" name="Update" value="Y">
    <?= bitrix_sessid_post(); ?>
    <? $tabControl->End(); ?>
</form>