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
$arAllOptions[] = Array("api_id", GetMessage("SMARTCAT_CONNECTOR_API_ID"), '', Array('text', 100));
$arAllOptions[] = Array("api_secret", GetMessage("SMARTCAT_CONNECTOR_API_SECRET"), '', Array('text', 100));
$arAllOptions[] = Array("api_server", GetMessage("SMARTCAT_CONNECTOR_API_SERVER"), \SmartCat\Client\SmartCat::SC_EUROPE, Array('selectbox', Array(
    \SmartCat\Client\SmartCat::SC_ASIA => GetMessage("SMARTCAT_CONNECTOR_SC_ASIA"),
    \SmartCat\Client\SmartCat::SC_EUROPE => GetMessage("SMARTCAT_CONNECTOR_SC_EUROPE"),
    \SmartCat\Client\SmartCat::SC_USA => GetMessage("SMARTCAT_CONNECTOR_SC_USA"),
)));

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

$arInfo = Array();
try{
    $acc_info = \Smartcat\Connector\Helper\ApiHelper::getAccount();
    if($acc_info){
        $arInfo[] = GetMessage("SMARTCAT_CONNECTOR_ACCOUNT") . ': ' . $acc_info->getName();
    }
}catch(\Exception $e){
    $apiId = \Bitrix\Main\Config\Option::get('smartcat.connector', 'api_id');
    $apiSecret = \Bitrix\Main\Config\Option::get('smartcat.connector', 'api_secret');
    if(!empty($apiId) || !empty($apiSecret) ){
        $msgError = GetMessage("SMARTCAT_CONNECTOR_ACCOUNT_ERROR_SERVER");
        if($e instanceof \Http\Client\Common\Exception\ClientErrorException){
            $msgError = GetMessage("SMARTCAT_CONNECTOR_ACCOUNT_ERROR_API");
        }
        CAdminMessage::ShowMessage($msgError);
    }
    $arInfo[] = GetMessage("SMARTCAT_CONNECTOR_ACCOUNT_NEED_SETTINGS");
}

$arInfo[] =  GetMessage("SMARTCAT_CONNECTOR_VERSIA_SHEMY") 
                .$schema->getCurrentVersion() . ' '
                .($schema->needUpgrade() ? '<a href="' . $APPLICATION->GetCurPageParam('db_upgrade=y') . '">'.GetMessage("SMARTCAT_CONNECTOR_OBNOVITQ_DO") . $schema->getLastVersion() . '</a>' : '');

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

    <? foreach($arInfo as $info):?>
    <tr>
        <td colspan="2" align="center">
            <?=$info;?>
        </td>
    </tr>
    <? endforeach ?>


    <? $tabControl->Buttons(); ?>
    <input type="submit" name="Update" <? if ($MOD_RIGHT < 'W') echo "disabled" ?>
           value="<? echo GetMessage('MAIN_SAVE') ?>">
    <input type="hidden" name="Update" value="Y">
    <?= bitrix_sessid_post(); ?>
    <? $tabControl->End(); ?>
</form>