<?IncludeModuleLangFile(__FILE__);?>
<form action="<? echo $APPLICATION->GetCurPage() ?>">
    <?= bitrix_sessid_post() ?>
    <input type="hidden" name="lang" value="<? echo LANG ?>">
    <input type="hidden" name="id" value="abbyy.cloud">
    <input type="hidden" name="uninstall" value="Y">
    <input type="hidden" name="step" value="2">
    <? echo CAdminMessage::ShowMessage(GetMessage("MOD_UNINST_WARN")) ?>
    <p><? echo GetMessage("MOD_UNINST_SAVE") ?></p>

    <p><input type="checkbox" name="save_tables" id="save_tables" value="Y" checked><label
            for="save_tables"><?=GetMessage("ABBYY_CLOUD_NE_UDALATQ_TABLICY")?></label></p>

    <input type="submit" name="inst" value="<? echo GetMessage("MOD_UNINST_DEL") ?>">
</form>