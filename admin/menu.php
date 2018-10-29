<?php
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
IncludeModuleLangFile(__FILE__);
?><?
IncludeModuleLangFile(__FILE__);

$APPLICATION->SetAdditionalCSS('/bitrix/css/smartcat.connector/smartcat.connector.css');

if ($APPLICATION->GetGroupRight("smartcat.connector") != "D") {
    $aMenu = array(
        "parent_menu" => "global_menu_services",
        "section" => "smartcat_connector",
        "sort" => 200,
        "text" => GetMessage("SMARTCAT_CONNECTOR_ALS"),
        "title" => GetMessage("SMARTCAT_CONNECTOR_ALS"),
        "icon" => "smartcat_connector_menu_icon",
        "page_icon" => "subscribe_page_icon",
        "items_id" => "menu_smartcat_connector",

        "items" => Array(
            Array(
                "url" => "smartcat.connector_profiles.php?lang=" . LANGUAGE_ID,
                "text" => GetMessage("SMARTCAT_CONNECTOR_PROFILI_PEREVODA"),
                "title" => GetMessage("SMARTCAT_CONNECTOR_PROFILI_PEREVODA"),
                "more_url" => Array(
                    'smartcat.connector_profile.php',
                ),
            ),
            Array(
                "url" => "smartcat.connector_tasks.php?lang=" . LANGUAGE_ID,
                "text" => GetMessage("SMARTCAT_CONNECTOR_ZAKAZY"),
                "title" => GetMessage("SMARTCAT_CONNECTOR_ZAKAZY"),
                "more_url" => Array(
                    'smartcat.connector_content.php',
                ),
            ),
        )
    );

    return $aMenu;
}
return false;
?>